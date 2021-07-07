<?php

namespace Icube\Logistix\Model\ResourceModel\ShipmentQueue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define collecion model
     */
    protected function _construct() {
        $this->_init(
        	'Icube\Logistix\Model\ShipmentQueue',
        	'Icube\Logistix\Model\ResourceModel\ShipmentQueue'
        	);
    }
}