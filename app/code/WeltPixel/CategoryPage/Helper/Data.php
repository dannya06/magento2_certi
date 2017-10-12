<?php

namespace WeltPixel\CategoryPage\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * @var array
     */
    protected $_categoryOptions;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        
        $this->_categoryOptions = $this->scopeConfig->getValue('weltpixel_category_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function displayReviews() {
        return $this->_categoryOptions['review']['display_reviews'];
    }

    /**
     * @return mixed
     */
    public function displayAddToWishlist() {
        return $this->_categoryOptions['general']['display_wishlist'];
    }

    /**
     * @return mixed
     */
    public function displayAddToCompare() {
        return $this->_categoryOptions['general']['display_compare'];
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function displaySwatches($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/general/display_swatches', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_categoryOptions['general']['display_swatches'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function displaySwatchTooltip($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/general/display_swatch_tooltip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_categoryOptions['general']['display_swatch_tooltip'];
        }
    }

    /**
     * @return mixed
     */
    public function isHoverImageEnabled() {
        return $this->_categoryOptions['image']['enable_hover_image'];
    }

    /**
     * @return mixed
     */
    public function displayAddToCart() {
        return $this->_categoryOptions['general']['display_addtocart'];
    }

    /**
     * @return mixed
     */
    public function alignAddToCart() {
        return $this->_categoryOptions['general']['addtocart_align'];
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getProductsPerLine($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/general/products_per_line', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_categoryOptions['general']['products_per_line'];
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getLayeredNavigationSwatchOptions($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/swatch_layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['swatch_layerednavigation'];
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingSwatchOptions($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/swatch_productlisting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['swatch_productlisting'];
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingItemOptions($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/item', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['item'];
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingNameOptions($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['name'];
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingPriceOptions($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/price', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['price'];
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingReviewOptions($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/review', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['review'];
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getToolbarOptions($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_category_page/toolbar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_productOptions['toolbar'];
        }
    }
}
