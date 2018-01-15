<?php
namespace Xtento\OrderExport\Controller\Adminhtml\Manual\ManualPost;

/**
 * Interceptor class for @see \Xtento\OrderExport\Controller\Adminhtml\Manual\ManualPost
 */
class Interceptor extends \Xtento\OrderExport\Controller\Adminhtml\Manual\ManualPost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Xtento\OrderExport\Helper\Module $moduleHelper, \Xtento\XtCore\Helper\Cron $cronHelper, \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Xtento\OrderExport\Model\ProfileFactory $profileFactory, \Xtento\OrderExport\Helper\Entity $entityHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Xtento\XtCore\Helper\Date $dateHelper, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Magento\Framework\Registry $registry, \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager, \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory, \Magento\Framework\Session\Config\ConfigInterface $sessionConfig, \Xtento\XtCore\Helper\Utils $utilsHelper, \Xtento\OrderExport\Model\ExportFactory $exportFactory)
    {
        $this->___init();
        parent::__construct($context, $moduleHelper, $cronHelper, $profileCollectionFactory, $scopeConfig, $profileFactory, $entityHelper, $storeManager, $dateHelper, $localeDate, $registry, $cookieManager, $cookieMetadataFactory, $sessionConfig, $utilsHelper, $exportFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
