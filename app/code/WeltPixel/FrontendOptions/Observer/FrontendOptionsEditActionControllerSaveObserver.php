<?php

namespace WeltPixel\FrontendOptions\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * FrontendOptionsEditActionControllerSaveObserver observer
 */
class FrontendOptionsEditActionControllerSaveObserver implements ObserverInterface {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $_dirReader;
    
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteFactory
     */
    protected $_writeFactory;
    
    /**
     * var \WeltPixel\FrontendOptions\Helper\Fonts
     */
    protected $_fontHelper;

    /**
     * var \WeltPixel\FrontendOptions\Helper\Data
     */
    protected $_helper;

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
     * Constructor
     *
     * @param \WeltPixel\FrontendOptions\Helper\Fonts $fontHelper
     * @param \WeltPixel\FrontendOptions\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \WeltPixel\FrontendOptions\Helper\Fonts $fontHelper,
        \WeltPixel\FrontendOptions\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_fontHelper = $fontHelper;
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_dirReader = $dirReader;
        $this->_writeFactory = $writeFactory;
        $this->_storeManager = $storeManager;
        $this->_storeCollection = $this->_storeManager->getStores();
    }

    /**
     * Save color options in file
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $frontendOptions = $this->_scopeConfig->getValue('weltpixel_frontend_options');
        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_FrontendOptions');
        
        $generatedCssDirectoryPath = 
            DIRECTORY_SEPARATOR . 'frontend'.
            DIRECTORY_SEPARATOR . 'web' .
            DIRECTORY_SEPARATOR . 'css' .
            DIRECTORY_SEPARATOR . 'source' .
            DIRECTORY_SEPARATOR . '_extend.less';
        
        $fontFamilyOptions = $this->_fontHelper->getFontFamilyOptions();
        $content = $this->_generateContent($frontendOptions, $fontFamilyOptions);
	    $content .= ".line-through (@a) when (@a = 1) {text-decoration: line-through;}";
        
        /** @var \Magento\Framework\Filesystem\Directory\WriteInterface|\Magento\Framework\Filesystem\Directory\Write $writer */
        $writer = $this->_writeFactory->create($directoryCode, \Magento\Framework\Filesystem\DriverPool::FILE);
        /** @var \Magento\Framework\Filesystem\File\WriteInterface|\Magento\Framework\Filesystem\File\Write $file */
        $file = $writer->openFile($generatedCssDirectoryPath, 'w');
        try {
            $file->lock();
            try {
                $file->write($content);
            } finally {
                $file->unlock();
            }
        } finally {
            $file->close();
        }


        /* Store view specific less generation */
        $this->_generateStoreViewSpecificLess();
    }

    /**
     * @return void
     */
    protected function _generateStoreViewSpecificLess() {
        $content = '/* Generated Less from WeltPixel_FrontendOptions */' . PHP_EOL;

        $lessTemplate = $this->_dirReader->getModuleDir('', 'WeltPixel_FrontendOptions') . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR . 'storeview_template.less';

        $lessVariables = $this->_getLessVariables();

        foreach ($this->_storeCollection as $store) {
            $lessValues = $this->_getLessValues($store);
            $content .= str_replace($lessVariables, $lessValues, file_get_contents($lessTemplate));
        }

        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_FrontendOptions');

        $lessPath =
            DIRECTORY_SEPARATOR . 'frontend'.
            DIRECTORY_SEPARATOR . 'web' .
            DIRECTORY_SEPARATOR . 'css' .
            DIRECTORY_SEPARATOR . 'source' .
            DIRECTORY_SEPARATOR . '_module.less';

        $writer = $this->_writeFactory->create($directoryCode, \Magento\Framework\Filesystem\DriverPool::FILE);
        $file = $writer->openFile($lessPath, 'w');
        try {
            $file->lock();
            try {
                $file->write($content);
            } finally {
                $file->unlock();
            }
        } finally {
            $file->close();
        }
    }
    
    
    /**
     * Generate the less css content for global frontend options
     * ____ in field attribute id must be replaced with -  
     * Magento is not allowing - character in id field, only this pattern'[a-zA-Z0-9_]{1,}'
     * 
     * @param aray $frontendOptions
     * @param array $fontFamilyOptions
     *
     * @return string
     */
    private function _generateContent($frontendOptions, $fontFamilyOptions) {
        $content = '// Generated Less from WeltPixel_FrontendOptions' . PHP_EOL;

        foreach ($frontendOptions as $groupId => $frontendGroup) {
            if (in_array($groupId, array('section_width'))) {
                continue;
            }
            foreach ($frontendGroup as $id => $frontendValue) {                
                //ignore _characterset admin options in frontend generation
                //they are used only in google font url creation
                $characterSetOption = strpos($id, '_characterset');
                if (($characterSetOption === false ) && trim(strlen($frontendValue))) {
                    if (in_array($id, $fontFamilyOptions)) {
                        if (!$frontendValue) { 
                            continue;
                        } else {
                            $frontendValue = "'" . $frontendValue ."', sans-serif";
                        }
                    }
                    /** add border css to color as well */
                    switch ($id) {
                        case 'button__border' :
                        case 'button__hover__border' :
                        $frontendValue .= ' 1px solid';
                            break;
                    }
                    $content .= '@'. str_replace('____', '-', $id) . ': ' . $frontendValue . ';'. PHP_EOL;
                }
            }
        }
        
        return $content;
    }

    /**
     * @return array
     */
    private function _getLessVariables() {
        return array(
            '@storeViewClass',
            '@pageMainWidth',
            '@pageMainPadding',
            '@footerWidth',
            '@rowWidth',
            '@defaultPageWidth',
            '@cmsPageWidth',
            '@productPageWidth',
            '@categoryPageWidth'
        );
    }

    /**
     * @param \Magento\Store\Model\Store
     * @return array
     */
    private function _getLessValues(\Magento\Store\Model\Store $store) {
        $storeId = $store->getStoreId();
        $storeCode = $store->getData('code');
        $storeClassName = '.store-view-' . preg_replace('#[^a-z0-9]+#', '-', strtolower($storeCode));

        return array(
            $storeClassName,
            $this->_helper->getPageMainWidth($storeId),
            $this->_helper->getPageMainPadding($storeId),
            $this->_helper->getFooterWidth($storeId),
            $this->_helper->getRowWidth($storeId),
            $this->_helper->getDefaultPageWidth($storeId),
            $this->_helper->getCmsPageWidth($storeId),
            $this->_helper->getProductPageWidth($storeId),
            $this->_helper->getCategoryPageWidth($storeId)
        );
    }
}
