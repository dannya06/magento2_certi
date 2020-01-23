<?php

/**
 * Get regions for region field
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Ui\Component\Listing\Column;

class Region implements \Magento\Framework\Option\ArrayInterface
{
    protected $_storeManager;

    public function __construct(\Magento\Framework\App\ResourceConnection $conn)
    {
        $this->conn = $conn;
    }

    public function toOptionArray()
    {
        $connection = $this->conn->getConnection();
        $regionSql = "SELECT * FROM directory_country_region WHERE country_id = 'ID' ORDER BY default_name ASC";
        $regionResult = $connection->fetchAll($regionSql);

        $options = [];
        
        foreach ($regionResult as $region) {
            $options[] = ['value' => $region['region_id'], 'label' => __($region['default_name'])];
        }

        return $options;
    }
}
