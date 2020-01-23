<?php

/**
 * Resource Data of `advance_rate` table
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Model\ResourceModel;

class RateData extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('advance_rate', 'id');
    }
}
