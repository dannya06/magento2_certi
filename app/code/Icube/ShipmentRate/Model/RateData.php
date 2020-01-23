<?php

/**
 * Model of `advance_rate`
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Model;

class RateData extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Icube\ShipmentRate\Model\ResourceModel\RateData');
    }
}
