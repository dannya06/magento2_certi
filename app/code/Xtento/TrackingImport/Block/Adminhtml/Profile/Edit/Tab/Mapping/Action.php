<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            MlbKB4xzfXDFlN04cZrwR1LbEaw8WMlnyA9rcd7bvA8=
 * Last Modified: 2016-04-11T12:58:55+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Profile/Edit/Tab/Mapping/Action.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Profile\Edit\Tab\Mapping;

class Action extends AbstractMapping
{
    public $mappingId = 'action';
    public $mappingModel = 'Xtento\TrackingImport\Model\Processor\Mapping\Action';
    public $fieldLabel = 'Action';
    public $valueFieldLabel = 'Value';
    public $hasDefaultValueColumn = true;
    public $hasValueColumn = false;
    public $defaultValueFieldLabel = 'Value';
    public $addFieldLabel = 'Add new action';
    public $addAllFieldLabel = 'Add all actions';
    public $selectLabel = '--- Select action ---';
}
