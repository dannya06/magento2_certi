<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            bY/Ft2U8dyxRjeo/M3VIOTeBSPY04gzxxlhY9eC916A=
 * Last Modified: 2016-02-25T15:11:48+00:00
 * File:          app/code/Xtento/OrderExport/Block/Adminhtml/Log/Grid/Renderer/Type.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Block\Adminhtml\Log\Grid\Renderer;

class Type extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options
{
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getExportType() != \Xtento\OrderExport\Model\Export::EXPORT_TYPE_EVENT) {
            return parent::render($row);
        } else {
            return parent::render($row)." (".__('Event').": ".$row->getExportEvent().")";
        }
    }
}
