<?php

/**
 * Get websites for website field
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Ui\Component\Listing\Column;

class Website implements \Magento\Framework\Option\ArrayInterface
{
    protected $_storeManager;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function getWebsites() {
        return $this->storeManager->getWebsites();
    }

    public function toOptionArray()
    {
        $websites = $this->getWebsites();

        $options = [];
        
        foreach ($websites as $website) {
            $options[] = ['value' => $website['website_id'], 'label' => __($website['name'])];
        }

        return $options;
    }
}
