<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            MlbKB4xzfXDFlN04cZrwR1LbEaw8WMlnyA9rcd7bvA8=
 * Last Modified: 2016-03-05T13:40:03+00:00
 * File:          app/code/Xtento/TrackingImport/Model/ResourceModel/Source.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\ResourceModel;

class Source extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('xtento_trackingimport_source', 'source_id');
    }
}
