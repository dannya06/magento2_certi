<?php

namespace WeltPixel\CustomHeader\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * CustomHeaderEditActionControllerSaveObserver observer
 */
class CustomHeaderEditActionControllerSaveObserver implements ObserverInterface
{

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
     * var \WeltPixel\CustomHeader\Helper\Data
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
     * var \WeltPixel\FrontendOptions\Helper\Data
     */
    protected $_frontendHelper;

    /**
     * @var string
     */
    protected $_mobileBreakPoint;

    /**
     * Constructor
     *
     * @param \WeltPixel\CustomHeader\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\FrontendOptions\Helper\Data $frontendHelper
     */
    public function __construct(
        \WeltPixel\CustomHeader\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\FrontendOptions\Helper\Data $frontendHelper
    )
    {
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_dirReader = $dirReader;
        $this->_writeFactory = $writeFactory;
        $this->_storeManager = $storeManager;
        $this->_storeCollection = $this->_storeManager->getStores();
        $this->_frontendHelper = $frontendHelper;
        $this->_mobileBreakPoint = $this->_frontendHelper->getBreakpointM();
    }

    /**
     * Save for each sore the css options in file
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_CustomHeader');

        foreach ($this->_storeCollection as $store) {

            $generatedCssDirectoryPath = DIRECTORY_SEPARATOR . 'frontend' .
                DIRECTORY_SEPARATOR . 'web' .
                DIRECTORY_SEPARATOR . 'css' .
                DIRECTORY_SEPARATOR . 'weltpixel_custom_header_' .
                $store->getData('code') . '.less';

            $content = $this->_generateContent($store->getData('store_id'));

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

        return $this;
    }


    /**
     * Generate the less css content for the header options
     *
     * @param int $storeId
     * @return string
     */
    private function _generateContent($storeId)
    {
        $content = '/* Generated Less from WeltPixel_CustomHeader */' . PHP_EOL;

        $content .= $this->_generateLogoLess($storeId);

        $topHeaderWidth = $this->_helper->getTopHeaderWidth($storeId);

        $topHeaderLinkColor = $this->_helper->getTopHeaderLinkColor($storeId);
        $topHeaderActiveLinkColor = $this->_helper->getTopHeaderActiveLinkColor($storeId);
        $topHeaderHoverLinkColor = $this->_helper->getTopHeaderHoverLinkColor($storeId);

        $topHeaderSubmenuLinkColor = $this->_helper->getTopHeaderSubmenuLinkColor($storeId);
        $topHeaderSubmenuHoverLinkColor = $this->_helper->getTopHeaderSubmenuHoverLinkColor($storeId);

        $topHeaderTextColor = $this->_helper->getTopHeaderTextColor($storeId);
        $topHeaderBackgroundColor = $this->_helper->getTopHeaderBackgroundColor($storeId);
        $topHeaderBorderBottomColor = $this->_helper->getTopHeaderBorderBottomColor($storeId);
        $middleHeaderWidth = $this->_helper->getMiddleHeaderWidth($storeId);
        $middleHeaderBackgroundColor = $this->_helper->getMiddleHeaderBackgroundColor($storeId);
        $bottomHeaderWidth = $this->_helper->getBottomHeaderWidth($storeId);
        $bottomHeaderPadding = $bottomHeaderWidth;
        $bottomHeaderBackgroundColor = $this->_helper->getBottomHeaderBackgroundColor($storeId);
        $bottomHeaderLinkColor = $this->_helper->getBottomHeaderLinkColor($storeId);
        $bottomHeaderHoverLinkColor = $this->_helper->getBottomHeaderHoverLinkColor($storeId);
        $bottomNavigationShadow = $this->_helper->getBottomNavigationShadow($storeId);

        $serachOptionsWidth = $this->_helper->getSerachOptionsWidth($storeId);
        $serachOptionsHeight = $this->_helper->getSerachOptionsHeight($storeId);
        $serachOptionsBorderWidth = $this->_helper->getSerachOptionsBorderWidth($storeId);
        $serachOptionsBorderStyle = $this->_helper->getSerachOptionsBorderStyle($storeId);
        $serachOptionsBorderColor = $this->_helper->getSerachOptionsBorderColor($storeId);
        $serachOptionsBackground = $this->_helper->getSerachOptionsBackground($storeId);
        $serachOptionsColor = $this->_helper->getSerachOptionsColor($storeId);
        $serachOptionsFontSize = $this->_helper->getSerachOptionsFontSize($storeId);

        $headerIconSize = $this->_helper->getHeaderIconSize($storeId);

        // ---
        $topHeaderWidth = strlen(trim($topHeaderWidth)) ? 'max-width:' . $topHeaderWidth . ' !important;' : '';

        $topHeaderLinkColor = strlen(trim($topHeaderLinkColor)) ? 'color:' . $topHeaderLinkColor . ';' : '';
        $topHeaderActiveLinkColor = strlen(trim($topHeaderActiveLinkColor)) ? '&:active { color: ' . $topHeaderActiveLinkColor . '; }' : '';
        $topHeaderHoverLinkColor = strlen(trim($topHeaderHoverLinkColor)) ? '&:hover { color: ' . $topHeaderHoverLinkColor . '; }' : '';

        $topHeaderSubmenuLinkColor = strlen(trim($topHeaderSubmenuLinkColor)) ? 'color:' . $topHeaderSubmenuLinkColor . ' !important;' : '';
        $topHeaderSubmenuHoverLinkColor = strlen(trim($topHeaderSubmenuHoverLinkColor)) ? '&:hover { color: ' . $topHeaderSubmenuHoverLinkColor . ' !important; }' : '';

        $topHeaderTextColor = strlen(trim($topHeaderTextColor)) ? 'color:' . $topHeaderTextColor . ' !important;' : '';
        $topHeaderBackgroundColor = strlen(trim($topHeaderBackgroundColor)) ? 'background-color:' . $topHeaderBackgroundColor . ' !important;' : '';
        $topHeaderBorderBottomColor = strlen(trim($topHeaderBorderBottomColor)) ? 'border-bottom: 1px solid ' . $topHeaderBorderBottomColor . ';' : '';
        $middleHeaderWidth = strlen(trim($middleHeaderWidth)) ? 'max-width:' . $middleHeaderWidth . ';' : '';
        $middleHeaderBackgroundColor = strlen(trim($middleHeaderBackgroundColor)) ? 'background-color:' . $middleHeaderBackgroundColor . ' !important;' : '';
        $bottomHeaderWidth = strlen(trim($bottomHeaderWidth)) ? 'max-width:' . $bottomHeaderWidth . ';' : '';
        $bottomHeaderPadding = strlen(trim($bottomHeaderPadding)) ? '@media (max-width: ' . $bottomHeaderPadding . '){ padding-right: 15px !important; padding-left: 15px !important; }' : '';
        $bottomHeaderBackgroundColor = strlen(trim($bottomHeaderBackgroundColor)) ? 'background-color:' . $bottomHeaderBackgroundColor . ' !important;' : 'background-color: transparent !important;';
        $bottomHeaderLinkColor = strlen(trim($bottomHeaderLinkColor)) ? 'color:' . $bottomHeaderLinkColor . ' !important;' : '';
        $bottomHeaderHoverLinkColor = strlen(trim($bottomHeaderHoverLinkColor)) ? '&:hover { color: ' . $bottomHeaderHoverLinkColor . ' !important; }' : '';
        $bottomNavigationShadow = strlen(trim($bottomNavigationShadow)) ? '-webkit-box-shadow: ' . $bottomNavigationShadow . '; -moz-box-shadow: ' . $bottomNavigationShadow . '; box-shadow: ' . $bottomNavigationShadow . ';' : '';

        $serachOptionsWidth = strlen(trim($serachOptionsWidth)) ? 'width: ' . $serachOptionsWidth . ';' : '';
        $serachOptionsHeight = strlen(trim($serachOptionsHeight)) ? 'height: ' . $serachOptionsHeight . ';' : '';
        $serachOptionsBorderWidth = unserialize($serachOptionsBorderWidth)['<%- _id %>'];
        $searchOBW = '';
        $true = false;
        foreach ($serachOptionsBorderWidth as $serachOptionsBorderWidth) {
            if ($serachOptionsBorderWidth) {
                $true = true;
            }
            $searchOBW[] .= $serachOptionsBorderWidth;
        }
        $searchOBW = implode(' ', $searchOBW);
        $serachOptionsBorderWidth = strlen(trim($true)) ? 'border-width: ' . $searchOBW . ';' : 'border-width: 1px;';
        $serachOptionsBorderStyle = strlen(trim($serachOptionsBorderStyle)) ? 'border-style: ' . $serachOptionsBorderStyle . ';' : 'border-style: solid;';
        $serachOptionsBorderColor = strlen(trim($serachOptionsBorderColor)) ? 'border-color: ' . $serachOptionsBorderColor . ';' : 'border-color: #000000;';
        $serachOptionsBackground = strlen(trim($serachOptionsBackground)) ? 'background-color: ' . $serachOptionsBackground . ';' : 'background-color: transparent;';
        $serachOptionsColor = strlen(trim($serachOptionsColor)) ? 'color: ' . $serachOptionsColor . ';' : 'color: #000000;';
        $serachOptionsFontSize = strlen(trim($serachOptionsFontSize)) ? 'font-size: ' . $serachOptionsFontSize . ';' : 'font-size: 15px;';

        $headerIconSize = strlen(trim($headerIconSize)) ? 'font-size: ' . $headerIconSize . ' !important;' : 'font-size: 16px !important;';

        //        Generate Less
        $content .= "
.page-wrapper .page-header {
    $middleHeaderBackgroundColor
    .panel.wrapper {
        $topHeaderBorderBottomColor
        $topHeaderBackgroundColor
        $topHeaderTextColor
    }
    .panel.header {
        $topHeaderWidth
        ul.header.links {
            li {
                > a {
                    $topHeaderLinkColor
                    $topHeaderActiveLinkColor
                    $topHeaderHoverLinkColor
                    &:visited {
                        $topHeaderLinkColor
                    }
                }
                ul.header.links {
                    padding: 10px;
	                li {
	                    a {
		                    $topHeaderSubmenuLinkColor
		                    &:visited {
		                        $topHeaderSubmenuLinkColor
		                    }
		                    $topHeaderSubmenuHoverLinkColor
	                    }
	                }
                }
            }
        }
    }
    // Middle
    .header.content, .header_right {
        $middleHeaderWidth
        $bottomHeaderPadding
        .block-search {
            input {
                $serachOptionsWidth
                $serachOptionsHeight
                $serachOptionsBorderWidth
                $serachOptionsBorderStyle
                $serachOptionsBorderColor
                $serachOptionsBackground
                $serachOptionsColor
                $serachOptionsFontSize
            }
        }
    }
    #switcher-language {
        ul {
            li {
                a {
                    $topHeaderSubmenuLinkColor
                    &:visited {
                        $topHeaderSubmenuLinkColor
                    }
                    $topHeaderSubmenuHoverLinkColor
                }
            }
        }
    }
    .header.links > li.authorization-link a:before,
    .minicart-wrapper .action.showcart:before, 
    .minicart-wrapper .action.showcart.active:before,
    .block-search .action.search:before {
        $headerIconSize
    }
}

.nav-sections {
    $bottomHeaderBackgroundColor
    .navigation {
        $bottomHeaderWidth
        $bottomHeaderBackgroundColor
        $bottomHeaderPadding
        ul {
            li {
                a {
                    $bottomHeaderLinkColor
                    $bottomHeaderHoverLinkColor
                    &:visited {
                        $bottomHeaderLinkColor
                    }
                }
            }
        }
    }
    @media (max-width: $this->_mobileBreakPoint) {
        background-color: white !important;
    }
    .nav-sections-item-content { 
        @media (min-width: $this->_mobileBreakPoint) {
            $bottomNavigationShadow
        }
    }
}
        ";
        return $content;
    }

    /**
     * Generate the less css content for the logo
     *
     * @param int $storeId
     * @return string
     */
    private function _generateLogoLess($storeId)
    {
        $content = '';
        $logoHeight = (int)$this->_scopeConfig->getValue(
            'design/header/logo_height',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $logoWidth = (int)$this->_scopeConfig->getValue(
            'design/header/logo_width',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($logoHeight || $logoWidth) {
            $logoImgSizeCss = '';

            if ($logoHeight) {
                $logoImgSizeCss .= "height: ${logoHeight}px;";
            }
            if ($logoWidth) {
                $logoImgSizeCss .= "width: ${logoWidth}px;";
            }


            $content .= "
@media (min-width: $this->_mobileBreakPoint) {
    :root .theme-pearl {
        .page-wrapper {
            .page-header {
                .logo {
                    img {
                        $logoImgSizeCss
                    }
                }
            }
        }
    }
}
            ";
        }

        return $content;

    }
}
