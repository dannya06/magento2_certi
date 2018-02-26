<?php

/**
 * Product:       Xtento_ProductExport (2.5.0)
 * ID:            cb9PRAWlxmJOwg/jsj5X3dDv0+dPZORkauC/n26ZNAU=
 * Packaged:      2018-02-26T09:11:39+00:00
 * Last Modified: 2016-04-14T15:37:35+00:00
 * File:          app/code/Xtento/ProductExport/Model/History.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Model;

/**
 * Class History
 * @package Xtento\ProductExport\Model
 */
class History extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Xtento\ProductExport\Model\ResourceModel\History');
        $this->_collectionName = 'Xtento\ProductExport\Model\ResourceModel\History\Collection';
    }
}