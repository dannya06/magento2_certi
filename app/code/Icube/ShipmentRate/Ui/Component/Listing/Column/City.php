<?php

/**
 * Get cities based on region_id for city field
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Ui\Component\Listing\Column;

class City implements \Magento\Framework\Option\ArrayInterface
{
    protected $_storeManager;

    public function __construct(\Magento\Framework\App\ResourceConnection $conn)
    {
        $this->conn = $conn;
    }

    public function toOptionArray($region_id = NULL)
    {
        $connection = $this->conn->getConnection();
        
        if (is_null($region_id)) {
            $region_id = $connection->query("SELECT region_id FROM directory_country_region WHERE country_id = 'ID' 
                ORDER BY default_name ASC LIMIT 1"
            )->fetch()['region_id'];
        }
        
        $cityResult = $connection->query(
            "SELECT city, kecamatan FROM city WHERE region_id = ? ORDER BY city ASC, kecamatan ASC", 
            [$region_id]
        )->fetchAll();

        $options = [];
        
        foreach ($cityResult as $city) {
            $value = $city['city'].', '.$city['kecamatan'];
            $options[] = ['value' => $value, 'label' => __($value)];
        }

        return $options;
    }
}
