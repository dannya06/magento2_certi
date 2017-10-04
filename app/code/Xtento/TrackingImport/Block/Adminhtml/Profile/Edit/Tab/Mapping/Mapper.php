<?php

/**
 * Product:       Xtento_TrackingImport (2.3.0)
 * ID:            HdWKOY0KdgGaRx+26HyONH06+SvSVZH7A2yQmSKRHJU=
 * Packaged:      2017-10-04T08:30:19+00:00
 * Last Modified: 2016-04-11T12:58:55+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Profile/Edit/Tab/Mapping/Mapper.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Profile\Edit\Tab\Mapping;

class Mapper extends AbstractMapping
{
    public $mappingId = 'mapping';
    public $mappingModel = 'Xtento\TrackingImport\Model\Processor\Mapping\Fields';
    public $fieldLabel = 'Magento Field';
    public $valueFieldLabel = 'File Field Name / Index';
    public $hasDefaultValueColumn = true;
    public $hasValueColumn = true;
    public $defaultValueFieldLabel = 'Default Value';
    public $addFieldLabel = 'Add field to mapping';
    public $addAllFieldLabel = 'Add all fields';
    public $selectLabel = '--- Select field ---';
}
