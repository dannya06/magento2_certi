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
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Backend\Model\Session\Proxy
     */
    protected  $_session;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * CustomHeaderEditActionControllerSaveObserver constructor.
     * @param \WeltPixel\CustomHeader\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\FrontendOptions\Helper\Data $frontendHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Model\Session\Proxy $session
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     */
    public function __construct(
        \WeltPixel\CustomHeader\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\FrontendOptions\Helper\Data $frontendHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session\Proxy $session,
        \Magento\Framework\Filesystem\DirectoryList $dir
    )
    {
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_dirReader = $dirReader;
        $this->_writeFactory = $writeFactory;
        $this->_storeManager = $storeManager;
        $this->_frontendHelper = $frontendHelper;
        $this->_messageManager = $messageManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_session = $session;
        $this->_dir = $dir;
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
        $this->_storeCollection = $this->_storeManager->getStores();
        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_CustomHeader');

        foreach ($this->_storeCollection as $store) {

            $this->_mobileBreakPoint = $this->_frontendHelper->getBreakpointM($store->getData('store_id'));

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

        /** Set only the notifications if triggered from admin save */
        $event = $observer->getEvent();
        if ($event instanceof \Magento\Framework\Event) {
            $eventName = $observer->getEvent()->getData();
            if ($eventName) {
                $url = $this->_urlBuilder->getUrl('adminhtml/cache');
                $message = __('Please regenerate Pearl Theme LESS/CSS files from <a href="%1">Cache Management Section</a>', $url);
                $this->_messageManager->addWarning($message);
                $this->_session->setWeltPixelCssRegeneration(true);
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


        $globalPromoTextColor = $this->_helper->getGlobalPromoTextColor($storeId);
        $globalPromoBackgroundColor = $this->_helper->getGlobalPromoBackgroundColor($storeId);

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

        // sticky header
        $stickyIsEnabled = $this->_helper->stickyHeaderIsEnabled($storeId);
        $stickyHeaderBackgroundColor = null;
        $stickyHeaderElementsColor = null;
        $stickyHeaderElementsHoverColor = null;
        $stickyNavigationBorderColor = null;
        $stickyNavigationBorderHoverColor = null;
        $stickySearchBorderColor = null;
        $stickySearchBackgroundColor = $this->_helper->getSerachOptionsBackground($storeId);
        if ($stickyIsEnabled) {
            $stickyAdvancedColors = $this->_helper->advancedColorsIsEnabled($storeId);
            if ($stickyAdvancedColors) {
                $stickyHeaderBackgroundColor = $this->_helper->getStickyHeaderBackgroundColor($storeId);
                $stickyHeaderElementsColor = $this->_helper->getStickyHeaderElementsColor($storeId);
                $stickyHeaderElementsHoverColor = $this->_helper->getStickyHeaderElementsHoverColor($storeId);
                $stickyNavigationBorderColor = $this->_helper->getStickyHeaderElementsColor($storeId);
                $stickyNavigationBorderHoverColor = $this->_helper->getStickyHeaderElementsHoverColor($storeId);
                $stickySearchBorderColor = $this->_helper->getStickyHeaderElementsColor($storeId);
                $stickySearchBackgroundColor = $this->_helper->getStickyHeaderBackgroundColor($storeId);
            }
        }

        $serachOptionsWidth = $this->_helper->getSerachOptionsWidth($storeId);
        $serachOptionsHeight = $this->_helper->getSerachOptionsHeight($storeId);
        $serachOptionsBorderWidth = $this->_helper->getSerachOptionsBorderWidth($storeId);
        $serachOptionsBorderStyle = $this->_helper->getSerachOptionsBorderStyle($storeId);
        $serachOptionsBorderColor = $this->_helper->getSerachOptionsBorderColor($storeId);
        $serachOptionsBackground = $this->_helper->getSerachOptionsBackground($storeId);
        $serachOptionsColor = $this->_helper->getSerachOptionsColor($storeId);
        $serachOptionsPlaceHolderColor = $this->_helper->getSerachOptionsPlaceHolderColor($storeId);
        $serachOptionsFontSize = $this->_helper->getSerachOptionsFontSize($storeId);

        $headerIconSize = $this->_helper->getHeaderIconSize($storeId);
        $headerIconColor = $this->_helper->getHeaderIconColor($storeId);
        $headerIconHoverColor = $this->_helper->getHeaderIconHoverColor($storeId);
        // ---

        $globalPromoTextColor = strlen(trim($globalPromoTextColor)) ? 'color: ' . $globalPromoTextColor . ';' : '';
        $globalPromoBackgroundColor = strlen(trim($globalPromoBackgroundColor)) ? 'background-color: ' . $globalPromoBackgroundColor . ';' : '';

        $topHeaderWidth = strlen(trim($topHeaderWidth)) ? 'max-width:' . $topHeaderWidth . ' !important;' : '';

        $topHeaderLinkColor = strlen(trim($topHeaderLinkColor)) ? 'color:' . $topHeaderLinkColor . ';' : '';
        $topHeaderActiveLinkColor = strlen(trim($topHeaderActiveLinkColor)) ? '&:active { color: ' . $topHeaderActiveLinkColor . '; }' : '';
        $topHeaderHoverLinkColor = strlen(trim($topHeaderHoverLinkColor)) ? '&:hover { color: ' . $topHeaderHoverLinkColor . ' !important; }' : '';

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

        if (!$serachOptionsBorderWidth) {
            $serachOptionsBorderWidth = [];
        } else {
            try {
                $serachOptionsBorderWidthJson = json_decode($serachOptionsBorderWidth);
                /** magento 2.2 removed serialization  */
                if ($serachOptionsBorderWidthJson && ($serachOptionsBorderWidth != $serachOptionsBorderWidthJson)) {
                    $serachOptionsBorderWidth = json_decode($serachOptionsBorderWidth, true);
                    $serachOptionsBorderWidth = $serachOptionsBorderWidth['<%- _id %>'];
                } else {
                    $serachOptionsBorderWidth = unserialize($serachOptionsBorderWidth)['<%- _id %>'];
                }
            } catch (\Exception $ex) {
                $serachOptionsBorderWidth = [];
            }
        }


        $searchOBW = [];
        $true = false;
        foreach ($serachOptionsBorderWidth as $serachOptionsBorderWidth) {
            if ($serachOptionsBorderWidth) {
                $true = true;
            }
            $searchOBW[] .= $serachOptionsBorderWidth;
        }
        $searchOBW = implode(' ', $searchOBW);
        $serachOptionsBorderWidth = strlen(trim($true)) ? 'border-width: ' . $searchOBW . ';' : 'border-width: 0px 0px 1px 0px;';
        $serachOptionsBorderStyle = strlen(trim($serachOptionsBorderStyle)) ? 'border-style: ' . $serachOptionsBorderStyle . ';' : 'border-style: solid;';
        $serachOptionsBorderColor = strlen(trim($serachOptionsBorderColor)) ? 'border-color: ' . $serachOptionsBorderColor . ';' : 'border-color: #000000;';
        $serachOptionsBackground = strlen(trim($serachOptionsBackground)) ? 'background-color: ' . $serachOptionsBackground . ';' : 'background-color: transparent;';
        $serachOptionsColor = strlen(trim($serachOptionsColor)) ? 'color: ' . $serachOptionsColor . ';' : 'color: initial;';
        $serachOptionsPlaceHolderColor = strlen(trim($serachOptionsPlaceHolderColor)) ? 'color: ' . $serachOptionsPlaceHolderColor . ';' : 'color: #000000;';
        $serachOptionsFontSize = strlen(trim($serachOptionsFontSize)) ? 'font-size: ' . $serachOptionsFontSize . ';' : 'font-size: 15px;';

        $headerIconSize = strlen(trim($headerIconSize)) ? 'font-size: ' . $headerIconSize . ' !important;' : 'font-size: 16px !important;';
        $headerIconColor = strlen(trim($headerIconColor)) ? 'color: ' . $headerIconColor . ' !important' : 'color: inherit';
        $headerIconHoverColor = strlen(trim($headerIconHoverColor)) ? 'color: ' . $headerIconHoverColor . ' !important' : 'color: inherit';

        $bkColorMobile = (int)$this->_mobileBreakPoint - 1 . 'px';

        // sticky header
        $stickyHeaderBackgroundColor = $stickyHeaderBackgroundColor && strlen(trim($stickyHeaderBackgroundColor)) ? 'background-color:' . $stickyHeaderBackgroundColor . ' !important;' : 'background-color: #ffffff !important;';
        $stickyHeaderElementsColor = $stickyHeaderElementsColor && strlen(trim($stickyHeaderElementsColor)) ? 'color:' . $stickyHeaderElementsColor . ' !important;' : '';
        $stickyHeaderElementsHoverColor = $stickyHeaderElementsHoverColor && strlen(trim($stickyHeaderElementsHoverColor)) ? 'color:' . $stickyHeaderElementsHoverColor . ' !important;' : '';
        $stickyNavigationBorderColor = $stickyNavigationBorderColor && strlen(trim($stickyNavigationBorderColor)) ? 'border-color:' . $stickyNavigationBorderColor . ' !important;' : '';
        $stickyNavigationBorderHoverColor = $stickyNavigationBorderHoverColor && strlen(trim($stickyNavigationBorderHoverColor)) ? 'border-color:' . $stickyNavigationBorderHoverColor . ' !important;' : '';
        $stickySearchBorderColor = $stickySearchBorderColor && strlen(trim($stickySearchBorderColor)) ? 'border-color:' . $stickySearchBorderColor . ' !important;' : '';
        $stickySearchBackgroundColor = $stickySearchBackgroundColor && strlen(trim($stickySearchBackgroundColor)) ? 'background-color:' . $stickySearchBackgroundColor . ' !important;' : '';

        //        Generate Less
        $content .= "
        .page-header-v2 {
	        .customer-welcome {
	            .customer-name {
	                span {
	                    display: none;
	                }
	                &:before {
	                    $headerIconColor;
	                }
	                &:hover {
	                    &:before {
	                        $headerIconHoverColor;
	                    }
                    }
	            }
	        }
	    }
	.page-wrapper .page-header {
        $middleHeaderBackgroundColor
        .block-search input::-webkit-input-placeholder {
            $serachOptionsPlaceHolderColor
        }
        .block-search input::-moz-placeholder {
            $serachOptionsPlaceHolderColor
        }
        .block-search input::-ms-placeholder {
            $serachOptionsPlaceHolderColor
        }
        .block-search input::placeholder
        {
            $serachOptionsPlaceHolderColor
        }
        .block-search .action.search:before
        {
            $headerIconColor;
        }
        .block-search .action.search:hover {
            &:before {
                $headerIconHoverColor;
            }
        }
        .panel.wrapper {
            $topHeaderBorderBottomColor
            $topHeaderBackgroundColor
        }
        .header-global-promo {
            .global-notification-wrapper {
                $globalPromoTextColor
                $globalPromoBackgroundColor
                a.close-global-notification {
                    $globalPromoTextColor
                }
            }
        }
        .panel.header {
            $topHeaderWidth
            ul.compare {
                li {
                    > a,
                    > a span {
                        &:visited {
                            $topHeaderLinkColor
                        }
                        $topHeaderLinkColor
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                }
            }
            ul.header.links {
                li {
                    > a,
                    span {
                        &:visited {
                            $topHeaderLinkColor
                        }
                        $topHeaderLinkColor
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                    &:after {
                        $topHeaderLinkColor
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                }
            }
            .switcher-currency,
            .switcher-language {
                strong {
                    $topHeaderLinkColor
                    $topHeaderActiveLinkColor
                    $topHeaderHoverLinkColor
                    span {
                        $topHeaderLinkColor
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                }
                .switcher-trigger {
                    &:after {
                        $topHeaderLinkColor
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                }
            }
        }
    // Middle
    .header-multistore .multistore-desktop .weltpixel_multistore {
        $middleHeaderWidth
    }
    .header.content,
    .header_right {
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
                &:focus{
                     $serachOptionsBorderColor
                }
            }
        }
    }
    .header.content {
        .nav-toggle {
            &:before {
                $headerIconColor
            }
            &:hover {
                &:before {
                    $headerIconColor
                }
            }
        }
        .block-search {
            .control {
                $middleHeaderBackgroundColor
            }
        }
    }
    #switcher-language,
    #switcher-currency {
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
    .header.links .authorization-link a:before,
	.minicart-wrapper .action.showcart:before {
	    $headerIconColor;
	}
	.header.links .authorization-link a:hover:before,
	.minicart-wrapper .action.showcart:hover:before {
	    $headerIconHoverColor;
	}
	.header.content,
	.header_right {
	    .field.search {
	        label {
	            $headerIconColor;
	        }
	    }
	}
}

.nav-sections {
    $bottomHeaderBackgroundColor
    .nav-sections-items {
        $bottomHeaderBackgroundColor
    }
    .navigation {
        $bottomHeaderWidth
        $bottomHeaderBackgroundColor
        $bottomHeaderPadding
        ul {
            li.level0 > a {
                $bottomHeaderLinkColor
                &:visited {
                    $bottomHeaderLinkColor
                }
                $bottomHeaderHoverLinkColor
                @media (max-width: $bkColorMobile) {
			        color: #575757 !important;
			    }
            }
        }
        @media (max-width: $bkColorMobile) {
	        background-color: inherit !important;
	    }
    }
    @media (max-width: $bkColorMobile) {
        background-color: white !important;
    }
    .nav-sections-item-content {
        @media (min-width: $bkColorMobile) {
            $bottomNavigationShadow
        }
    }
}
// Sticky Header
.page-header.sticky-header,
.page-header.sticky-header-mobile {
    $stickyHeaderBackgroundColor
    .page-header {
        $stickyHeaderBackgroundColor
    }
    .panel.wrapper {
        $stickyHeaderBackgroundColor
    }
    .header.links {
        $stickyHeaderElementsColor
        li > a {
            $stickyHeaderElementsColor
            &:visited {
                $stickyHeaderElementsColor
                &:hover {
                    $stickyHeaderElementsHoverColor
                }
            }
            &:hover {
                $stickyHeaderElementsHoverColor
            }
        }
        li:after {
            $stickyHeaderElementsColor
        }
    }
    .navigation ul li.level0 > a,
    .navigation ul li.level0 > a:visited {
        $stickyHeaderElementsColor
        $stickyNavigationBorderColor
        &:hover {
            $stickyHeaderElementsHoverColor
            $stickyNavigationBorderHoverColor
        }
        li > a,
        li > a:visited {
            $stickyHeaderElementsColor
            &:hover {
                $stickyHeaderElementsHoverColor
            }
        }
    }
    .header_right {
        .block-search input::-webkit-input-placeholder { $stickyHeaderElementsColor }
        .block-search input::-moz-placeholder { $stickyHeaderElementsColor }
        .block-search input:-ms-input-placeholder { $stickyHeaderElementsColor }
        .block-search input:-moz-placeholder { $stickyHeaderElementsColor }
        .block-search {
            input {
                $stickyHeaderElementsColor
                $stickySearchBorderColor
            }
            .action.search {
                &:before {
                    $stickyHeaderElementsColor
                }
                &:hover {
                    &:before {
                        $stickyHeaderElementsHoverColor
                    }
                }
            }
        }
    }
    .header.content {
        .block-search {
            .field.search {
                label {
                    $stickyHeaderElementsColor
                }
                .control {
                    $stickySearchBackgroundColor
                }
            }
        }
    }
    .page-header,
    .header.content,
    .header.links .authorization-link a {
        &:before {
            $stickyHeaderElementsColor
        }
        &:hover {
            $stickyHeaderElementsHoverColor
            &:before {
                $stickyHeaderElementsHoverColor
            }
        }
        .nav-toggle,
        .minicart-wrapper .action.showcart {
            &:before {
                $stickyHeaderElementsColor
            }
            &:hover {
                $stickyHeaderElementsHoverColor
                &:before {
                    $stickyHeaderElementsHoverColor
                }
            }
        }
        .block-search {
            .control {
                $stickySearchBackgroundColor
            }
            input {
                $stickySearchBackgroundColor
                $stickyHeaderElementsColor
                $stickySearchBorderColor
                &::-webkit-input-placeholder { $stickyHeaderElementsColor }
                &::-moz-placeholder { $stickyHeaderElementsColor }
                &:-ms-input-placeholder { $stickyHeaderElementsColor }
                &:-moz-placeholder { $stickyHeaderElementsColor }
            }
            .action.search {
                &:before {
                    $stickyHeaderElementsColor
                }
                &:hover {
                    &:before {
                        $stickyHeaderElementsHoverColor
                    }
                }
            }
        }
    }
}
.page-header.page-header-v4.sticky-header {
    .header.content {
        z-index: 1;
    }
    .panel.wrapper {
        z-index: 10;
        background-color: transparent !important;
        #switcher-currency {
            &.switcher {
                .toggle.switcher-trigger {
                    &:after {
                        $stickyHeaderElementsColor
                    }
                    &:hover {
                        $stickyHeaderElementsHoverColor
                        &:after {
                            $stickyHeaderElementsHoverColor
                        }
                    }
                }
                strong {
                    $stickyHeaderElementsColor
                    &:hover {
                        $stickyHeaderElementsHoverColor
                    }
                }
            }
        }
        .panel.header{
            .header.links {
                li {
                    $stickyHeaderElementsColor
                    & > a,
                    & > span {
                        $stickyHeaderElementsColor
                        &:visited {
                            $stickyHeaderElementsColor
                        }
                        &:hover {
                            $stickyHeaderElementsHoverColor
                        }
                    }
                }
            }
            .switcher-currency{
                display: none;
            }
        }
    }
}
.nav-sections.sticky-header {
    $stickyHeaderBackgroundColor
    padding-bottom: 0 !important;
    .nav-sections-item-content {
        $stickyHeaderBackgroundColor
    }
    .navigation {
        $stickyHeaderBackgroundColor
        ul li.level0 > a,
        ul li.level0 > a:visited {
            $stickyHeaderElementsColor
            $stickyNavigationBorderColor
            &:hover {
                $stickyHeaderElementsHoverColor
                $stickyNavigationBorderHoverColor
            }
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

        $logoWidth = (int)$this->_scopeConfig->getValue(
            'design/header/logo_width',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $logoHeight = (int)$this->_scopeConfig->getValue(
            'design/header/logo_height',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $logoSrc = $this->_scopeConfig->getValue(
            'design/header/logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $logoRatio = false;
        if ($logoSrc) {
            $logoSrc = $this->_dir->getPath('media') . DIRECTORY_SEPARATOR . 'logo' . DIRECTORY_SEPARATOR . $logoSrc;
            $imgPathArr = explode('.', $logoSrc);
            $imgType = end($imgPathArr);

            if ($imgType != 'svg') {
                list($width, $height) = getimagesize($logoSrc);
                $logoRatio = $width / $height;
            } else {
                $xml = simplexml_load_file($logoSrc);
                $attr = $xml->attributes();
                if ($attr->width && $attr->height) {
                    $logoRatio = $attr->width / $attr->height;
                }
            }
        }

        if ($logoRatio && ($logoWidth || $logoHeight)) {
            $logoImgSizeCss = '';

            if ($logoWidth && !$logoHeight) {
                $logoHeight = (int) ($logoWidth / $logoRatio);
            } elseif (!$logoWidth && $logoHeight) {
                $logoWidth = (int) ($logoHeight * $logoRatio);
            }

            $logoImgSizeCss .= "width: ${logoWidth}px;";
            $logoImgSizeCss .= "height: ${logoHeight}px;";

            /** This is for the admin image width height proper usage */
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

        // sticky header logo width and height
        if ($logoRatio) {
            $stickyLogoHeight = 34;
            $stickyLogoWidth = (int) ($stickyLogoHeight * $logoRatio);
            $stickyLogoImgSizeCss = '';
            $stickyLogoImgSizeCss .= "width: ${stickyLogoWidth}px;";
            $stickyLogoImgSizeCss .= "height: ${stickyLogoHeight}px;";

            $content .= "
@media (min-width: $this->_mobileBreakPoint) {
    :root .theme-pearl {
        .page-wrapper {
            .page-header.sticky-header {
                .logo {
                    img {
                        $stickyLogoImgSizeCss
                    }
                }
            }
        }
    }
}
            ";
            $stickyMenuOffsetLeft = $stickyLogoWidth > 100 ? 146 * -1 : ($stickyLogoWidth + 46) * -1;

            $stickyMenuOffsetLeftCss = "left: ${stickyMenuOffsetLeft}px !important;";

            $content .= "
                .page-header-v2.sticky-header,
                .page-header-v1.sticky-header {
                    .megamenu.level-top-fullwidth {
                        .fullwidth {
                            $stickyMenuOffsetLeftCss
                        }
                    }
                }
            ";
        }

        return $content;

    }
}
