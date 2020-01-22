<?php

/**
 * Collection of `advance_rate` table
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Model\ResourceModel\RateData;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Icube\ShipmentRate\Model\RateData', 'Icube\ShipmentRate\Model\ResourceModel\RateData');
    }
}
