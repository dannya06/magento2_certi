<?php

/**
 * Product:       Xtento_XtCore (2.0.9)
 * ID:            vuwMiuqT6hJFCgwIsMBM7iJwY9/E3ScMI/mHOqvUFvQ=
 * Packaged:      2017-10-04T08:30:08+00:00
 * Last Modified: 2017-08-16T08:52:13+00:00
 * File:          app/code/Xtento/XtCore/Model/System/Config/Source/Order/AllStatuses.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Model\System\Config\Source\Order;

/**
 * Class AllStatuses
 *
 * Order Statuses source model
 *
 * @package Xtento\XtCore\Model\System\Config\Source\Order
 */
class AllStatuses extends \Magento\Sales\Model\Config\Source\Order\Status
{
    protected $_stateStatuses = false; // Get all statuses, see \Magento\Sales\Model\Config\Source\Order\Status

    /**
     * Function to just put all order status "codes" into an array.
     *
     * @return array
     */
    public function toArray()
    {
        $statuses = $this->toOptionArray();
        $statusArray = [];
        foreach ($statuses as $status) {
            array_push($statusArray, $status['value']);
        }
        return $statusArray;
    }

}
