<?php
namespace WeltPixel\ProductPage\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddUpdateHandlesObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    const XML_PATH_PRODUCTPAGE_REMOVE_STOCK_AVAILABILITY = 'weltpixel_product_page/general/remove_stock_availability';
    const XML_PATH_PRODUCTPAGE_REMOVE_BREADCRUMBS = 'weltpixel_product_page/general/remove_breadcrumbs';
    const XML_PATH_PRODUCTPAGE_MOVE_TABS = 'weltpixel_product_page/general/move_description_tabs_under_info_area';
    const XML_PATH_PRODUCTPAGE_VERSION = 'weltpixel_product_page/version/version';

    /**
     * AddUpdateHandlesObserver constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');
        $fullActionName = $observer->getData('full_action_name');

        if ($fullActionName != 'catalog_product_view') {
            return $this;
        }

        $removeAvailability = $this->scopeConfig->getValue(self::XML_PATH_PRODUCTPAGE_REMOVE_STOCK_AVAILABILITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $removeBreadcrumbs = $this->scopeConfig->getValue(self::XML_PATH_PRODUCTPAGE_REMOVE_BREADCRUMBS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $moveTabseAvailability = $this->scopeConfig->getValue(self::XML_PATH_PRODUCTPAGE_MOVE_TABS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $version = $this->scopeConfig->getValue(self::XML_PATH_PRODUCTPAGE_VERSION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($removeAvailability) {
            $layout->getUpdate()->addHandle('weltpixel_productpage_removeavailability');
        }

        if ($removeBreadcrumbs) {
            $layout->getUpdate()->addHandle('weltpixel_productpage_removebreadcrumbs');
        }

        if ($moveTabseAvailability) {
            $layout->getUpdate()->addHandle('weltpixel_productpage_movetabs');
        }

        if ($version == 2) {
            $layout->getUpdate()->addHandle('catalog_product_view_v2');
        }

        if ($version == 3) {
            $layout->getUpdate()->addHandle('catalog_product_view_v3');
        }

        if ($version == 4) {
            $layout->getUpdate()->addHandle('catalog_product_view_v4');
        }

        return $this;
    }
}
