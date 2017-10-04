<?php

/**
 * Product:       Xtento_OrderExport (2.3.7)
 * ID:            vuwMiuqT6hJFCgwIsMBM7iJwY9/E3ScMI/mHOqvUFvQ=
 * Packaged:      2017-10-04T08:30:08+00:00
 * Last Modified: 2015-11-26T12:57:04+00:00
 * File:          app/code/Xtento/OrderExport/Model/ResourceModel/Profile/Collection.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\ResourceModel\Profile;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Xtento\OrderExport\Model\Profile', 'Xtento\OrderExport\Model\ResourceModel\Profile');
    }
}