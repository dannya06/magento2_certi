<?php

namespace WeltPixel\ProductPage\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var array
     */
    protected $_productOptions;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        
        $this->_productOptions = $this->scopeConfig->getValue('weltpixel_product_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return boolean
     */
    public function removeWishlist() {
        return $this->_productOptions['general']['remove_wishlist'];
    }

    /**
     * @return boolean
     */
    public function removeCompare() {
        return $this->_productOptions['general']['remove_compare'];
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getImageAreaWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_product_page/general/image_area_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['general']['image_area_width'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getProductInfoAreaWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_product_page/general/product_info_area_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['general']['product_info_area_width'];
        }
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function removeTabsBorder($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_product_page/general/remove_tabs_border', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['general']['remove_tabs_border'];
        }
    }


    /**
     * @param int $storeId
     * @return boolean
     */
    public function removeSwatchTooltip($storeId = 0) {
        if ($storeId) {
            return !$this->scopeConfig->getValue('weltpixel_product_page/general/display_swatch_tooltip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return !$this->_productOptions['general']['display_swatch_tooltip'];
        }
    }


    /**
     * @param int $storeId
     * @return boolean
     */
    public function getTabsLayout($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_product_page/general/tabs_layout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['general']['tabs_layout'];
        }
    }


    /**
     * @param int $storeId
     * @return boolean
     */
    public function getQtySelectMaxValue($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_product_page/general/qty_select_maxvalue', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['general']['qty_select_maxvalue'];
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getSwatchOptions($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_product_page/swatch', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['swatch'];
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getCssOptions($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_product_page/css', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['css'];
        }
    }

	/**
	 * @param int $storeId
	 * @return mixed
	 */
	public function getBackgroundArrows($storeId = 0) {
		if ($storeId) {
			return $this->scopeConfig->getValue('weltpixel_product_page/gallery/arrows_bg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		} else {
			return $this->_productOptions['gallery']['arrows_bg'];
		}
	}
}
