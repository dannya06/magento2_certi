<?php

namespace WeltPixel\CustomHeader\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_headerOptions;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        
        $this->_headerOptions = $this->scopeConfig->getValue('weltpixel_custom_header', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['top_header']['width'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderLinkColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['top_header']['link_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderHoverLinkColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/hover_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['top_header']['hover_link_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderActiveLinkColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/active_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['top_header']['active_link_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderSubmenuLinkColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/submenu_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['top_header']['submenu_link_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderSubmenuHoverLinkColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/submenu_hover_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['top_header']['submenu_hover_link_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderTextColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/text_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['top_header']['text_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderBackgroundColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/background_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['top_header']['background_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderBorderBottomColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/border_bottom_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['top_header']['border_bottom_color'];
        }
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMiddleHeaderWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/middle_header/width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['middle_header']['width'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMiddleHeaderBackgroundColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/middle_header/background', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['middle_header']['background'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomHeaderWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['bottom_header']['width'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomHeaderBackgroundColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/background', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['bottom_header']['background'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomHeaderLinkColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['bottom_header']['link_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomHeaderHoverLinkColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/hover_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['bottom_header']['hover_link_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomNavigationShadow($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/shadow', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['bottom_header']['shadow'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['search_options']['width'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsHeight($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/height', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['search_options']['height'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsBorderWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/border_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['search_options']['border_width'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsBorderStyle($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/border_style', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['search_options']['border_style'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsBorderColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/border_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['search_options']['border_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsBackground($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/background', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['search_options']['background'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsColor($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['search_options']['color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsFontSize($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/font_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_headerOptions['search_options']['font_size'];
        }
    }

	/**
	 * @param int $storeId
	 * @return mixed
	 */
	public function getHeaderIconSize($storeId = 0) {
		if ($storeId) {
			return $this->scopeConfig->getValue('weltpixel_custom_header/icons/icon_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		} else {
			return $this->_headerOptions['icons']['icon_size'];
		}
	}
}
