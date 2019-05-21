<?php
namespace NS8\CSP\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    protected $scopeConfig;
    private $scopeWriter;
    private $encryptor;
    private $backendUrl;
    private $productMetadata;
    private $moduleList;
    private $storeManager;
    private $state;
    private $cacheTypeList;
    private $request;
    protected $cookieManager;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $scopeWriter,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Module\ModuleList $moduleList,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
    ) {
        $this->state = $state;
        $this->scopeConfig = $scopeConfig;
        $this->scopeWriter = $scopeWriter;
        $this->encryptor = $encryptor;
        $this->storeManager = $storeManager;
        $this->backendUrl = $backendUrl;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
        $this->cacheTypeList = $cacheTypeList;
        $this->request = $request;
        $this->cookieManager = $cookieManager;
    }

    public function flushConfigCache()
    {
        $this->cacheTypeList->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
    }

    public function getAdminUrl($path, $params = null)
    {
        return $this->backendUrl->getUrl($path, $params);
    }

    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }

    public function getExtensionVersion()
    {
        return $this->moduleList->getOne('NS8_CSP')['setup_version'];
    }

    //  needed for install/upgrade routines - do not call from anywhere else
    public function setAdminAreaCode()
    {
        try {
	        if (!isset($this->state->_areaCode)) {
		        $this->state->setAreaCode('adminhtml');
	        }
        } catch (\Exception $e) {
            // intentionally left empty
        }
    }

    public function getStores()
    {
        $result = [];
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            array_push($result, [
                'id' => $store->getId(),
                'websiteId' => $store->getWebsiteId(),
                'code' => $store->getCode(),
                'name' => $store->getName(),
                'groupId' => $store->getStoreGroupId(),
                'isActive' => $store->isActive(),
                'url' => $store->getCurrentUrl(true)
            ]);
        }
        return $result;
    }

    public function getStore()
    {
        $store = $this->storeManager->getStore();

        $data = [
            'id' => $store->getId(),
            'websiteId' => $store->getWebsiteId(),
            'code' => $store->getCode(),
            'name' => $store->getName(),
            'groupId' => $store->getStoreGroupId(),
            'isActive' => $store->isActive(),
            'url' => $store->getCurrentUrl(true)
        ];
        return $data;
    }

    public function getStoreId()
    {
        $store = $this->storeManager->getStore();
        return $store->getId();
    }

    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAccessToken()
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue('ns8/csp/token'));
    }

    public function setAccessToken($value)
    {
        $this->scopeWriter->save('ns8/csp/token', $this->encryptor->encrypt($value));
        $this->flushConfigCache();
        return true;
    }

    public function getProjectId()
    {
        return $this->scopeConfig->getValue('ns8/csp/projectid');
    }

    public function setProjectId($value)
    {
        $this->scopeWriter->save('ns8/csp/projectid', $value);
        $this->flushConfigCache();
        return true;
    }

    public function getApiBaseUrl()
    {
        $url = $this->encryptor->decrypt($this->scopeConfig->getValue('ns8/csp/apibaseurl'));

        if (isset($url) && $url !== "") {
            return $url;
        } else {
            return 'https://api.ns8.com/v1';
        }
    }

    public function setApiBaseUrl($value)
    {
        $this->scopeWriter->save('ns8/csp/apibaseurl', $this->encryptor->encrypt($value));
        $this->flushConfigCache();
        return true;
    }

    public function getWebsiteBaseUrl()
    {
        $url = $this->encryptor->decrypt($this->scopeConfig->getValue('ns8/csp/websitebaseurl'));

        if (isset($url) && $url !== "") {
            return $url;
        } else {
            return 'https://'.$this->getProjectId().'.magento-protect.ns8.com';
        }
    }

    public function setWebsiteBaseUrl($value)
    {
        $this->scopeWriter->save('ns8/csp/websitebaseurl', $this->encryptor->encrypt($value));
        $this->flushConfigCache();
        return true;
    }

    public function getStoreBaseUrl($store_id)
    {
        return $this->storeManager->getStore($store_id)->getBaseUrl();
    }

    public function resetEndpoints()
    {
        $this->scopeWriter->delete('ns8/csp/apibaseurl');
        $this->scopeWriter->delete('ns8/csp/websitebaseurl');
        $this->flushConfigCache();
        return true;
    }

    public function getCookie($name)
    {
        return $this->cookieManager->getCookie($name);
    }

    public function remoteAddress()
    {
        $xf = $this->request->getServer('HTTP_X_FORWARDED_FOR');

        if (!isset($xf)) {
            $xf = '';
        }

        $remoteAddr = $this->request->getServer('REMOTE_ADDR');

        if (!isset($remoteAddr)) {
            $remoteAddr = '';
        }

        if (isset($xf) && trim($xf) != '') {
            $xf = trim($xf);
            $xfs = [];

            //  see if multiple addresses are in the XFF header
            if (strpos($xf, '.') !== false) {
                $xfs = explode(',', $xf);
            } elseif (strpos($xf, ' ') !== false) {
                $xfs = explode(' ', $xf);
            }

            if (!empty($xfs)) {
                $count = count($xfs);

                //  get first public address, since multiple private routings can occur and be added to forwarded list
                for ($i = 0; $i < $count; $i++) {
                    $ipTrim = trim($xfs[$i]);

                    if (substr($ipTrim, 0, 7) == '::ffff:' && count(explode('.', $ipTrim)) == 4) {
                        $ipTrim = substr($ipTrim, 7);
                    }

                    if ($ipTrim != "" && substr($ipTrim, 0, 3) != "10."
                        && substr($ipTrim, 0, 7) != "172.16."
                        && substr($ipTrim, 0, 7) != "172.31."
                        && substr($ipTrim, 0, 8) != "127.0.0."
                        && substr($ipTrim, 0, 8) != "192.168." && $ipTrim != "unknown" && $ipTrim != "::1") {
                        return ($ipTrim);
                    }
                }
                $xf = trim($xfs[0]);
            }

            if (substr($xf, 0, 7) == '::ffff:' && count(explode('.', $xf)) == 4) {
                $xf = substr($xf, 7);
            }

            //  a tiny % of hits have an unknown ip address
            if (substr($xf, 0, 7) == "unknown") {
                return "127.0.0.1";
            }

            return ($xf);
        } else {
            //  a tiny % of hits have an unknown ip address, so return a default address
            if (substr($remoteAddr, 0, 7) == "unknown") {
                return "127.0.0.1";
            }

            return ($remoteAddr);
        }
    }
}
