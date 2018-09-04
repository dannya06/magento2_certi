<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Icube\Bundle\Controller\Adminhtml\Cache;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Directory\Helper\Data;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\Exception\LocalizedException;

class GenerateJsBundle extends \Magento\Backend\Controller\Adminhtml\Cache
{

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \WeltPixel\Command\Model\GenerateCss
     */
    protected $generateCss;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var  ThemeProviderInterface */
    protected $themeProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Store collection
     * storeId => \Magento\Store\Model\Store
     *
     * @var array
     */
    protected $_storeCollection;

    /**
     * @var \Magento\Framework\View\Asset\MergeService
     */
    protected $mergeService;

    /**
     * @var \Magento\Framework\App\Cache\Manager
     */
    protected $cacheManager;

    /**
     * GenerateCss constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \WeltPixel\Command\Model\GenerateCss $generateCss
     * @param ScopeConfigInterface $scopeConfig
     * @param ThemeProviderInterface $themeProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Asset\MergeService $mergeService
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \WeltPixel\Command\Model\GenerateCss $generateCss,
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\MergeService $mergeService,
        \Magento\Framework\App\Cache\Manager $cacheManager
    )
    {
        parent::__construct($context, $cacheTypeList, $cacheState, $cacheFrontendPool, $resultPageFactory);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->generateCss = $generateCss;
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
        $this->_storeManager = $storeManager;
        $this->mergeService = $mergeService;
        $this->cacheManager = $cacheManager;
        $this->_storeCollection = $storeManager->getStores();
    }

    /**
     * Generate the JS bundles files from admin button trigger
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {	
	    // module enable/disable config
	   	$isEnableJsBundling = $this->scopeConfig->getValue('icube_bundle/bundle_js/bundle_js_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    	    	    
	    if($isEnableJsBundling) {
		    $command = "node -v";
		    $nodeV = shell_exec($command);
		    $node = '';
		    $nodePath = '';
		    if(!$nodeV) {
			    $command = "nodejs -v";
				$nodeV = shell_exec($command);
				if($nodeV){
					$node = 'nodejs';
				}
		    }else{
			    $node = 'node';
		    }
		    if($node) {
			    $command = "which $node";
				$nodePath = shell_exec($command);
		    }
		    if($node && $nodePath) {
		        try {
				    // generating asset path 
				    $staticContenteUrl = $this->_storeManager->getStore()->getBaseStaticDir();
				    $themeId = $this->scopeConfig->getValue(
			            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
			            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
			            $this->_storeManager->getStore()->getId()
			        );
			        $theme = $this->themeProvider->getThemeById($themeId);
				    $assetFolder = $staticContenteUrl . '/'. $theme->getFullPath();
				    
					$stores = $this->_storeManager->getStores($withDefault = false);
					
					// collect commands for all stores
					$commands = [];
					foreach($stores as $store) {
					    $locale = $this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId());
// 					    $command = $nodePath." pub/js-bundle/dist/r.js -o pub/js-bundle/build/build.js baseUrl=".$assetFolder."/".$locale."_source dir=".$assetFolder."/".$locale;
					    $command = "bash generate_bundle.sh '".$assetFolder."/".$locale."_source' '".$assetFolder."/".$locale."' '".$nodePath."'";
					    $commands[] = $command;
					}

					if($commands) {
						// merge commands into 1 line then run it
						$command = implode($commands,' && ');
						$command = preg_replace( "/\r|\n/", "", $command );	// remove line breaks
// 						var_dump($command);die;
			            $data = '<pre>'.shell_exec($command).'</pre>';
						$this->messageManager->addSuccess(__($data));
					}else{
						$this->messageManager->addSuccess(__('Nothing is generated'));
					}
				} catch (Exception $e) {
					die('error');
				    $this->messageManager->addError($e->getMessage());
				}
		    }else{
			    $this->messageManager->addError(__('Node does not exist'));
		    }
	    }else{
		    $this->messageManager->addError(__('JS Bundling is disabled'));
		}
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('adminhtml/*');
    }

}
