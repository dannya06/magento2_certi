<?php

namespace Icube\CustomStyle\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * CustomStyleEditActionControllerSaveObserver observer
 */
class CustomStyleEditActionControllerSaveObserver implements ObserverInterface {

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
     * var \Icube\CustomStyle\Helper\Data
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
     * @param \Icube\CustomStyle\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \WeltPixel\FrontendOptions\Helper\Fonts $fontHelper,
        \Icube\CustomStyle\Helper\Data $helper,
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
     * Save custom less code in file
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $frontendOptions = $this->_scopeConfig->getValue('icube_customstyle');
        $directoryCode = $this->_dirReader->getModuleDir('view', 'Icube_CustomStyle');
        
        $generatedCssDirectoryPath = 
            DIRECTORY_SEPARATOR . 'frontend'.
            DIRECTORY_SEPARATOR . 'web' .
            DIRECTORY_SEPARATOR . 'css' .
            DIRECTORY_SEPARATOR . 'source' .
            DIRECTORY_SEPARATOR . '_extend.less';
        
        $fontFamilyOptions = $this->_fontHelper->getFontFamilyOptions();
        $content = $this->_generateContent($frontendOptions, $fontFamilyOptions);
        
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
        if ($frontendOptions) {
            $content = '// Generated Less from Icube_CustomStyle' . PHP_EOL;

            foreach ($frontendOptions as $groupId => $frontendGroup) {
                if (in_array($groupId, array('section_width'))) {
                    continue;
                }
                foreach ($frontendGroup as $id => $frontendValue) {                
                    $content .= '' . PHP_EOL;
                    $content .= '/*----------------------------------------*/ '. PHP_EOL;
                    $content .= '/* '. $id  . PHP_EOL;
                    $content .= '/*----------------------------------------*/ '. PHP_EOL;
                    $content .= $frontendValue;
                }
            }
        }else{
            $content = '';
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

}
