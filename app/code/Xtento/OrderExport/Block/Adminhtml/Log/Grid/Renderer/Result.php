<?php

/**
 * Product:       Xtento_OrderExport (2.3.7)
 * ID:            vuwMiuqT6hJFCgwIsMBM7iJwY9/E3ScMI/mHOqvUFvQ=
 * Packaged:      2017-10-04T08:30:08+00:00
 * Last Modified: 2015-09-07T19:26:16+00:00
 * File:          app/code/Xtento/OrderExport/Block/Adminhtml/Log/Grid/Renderer/Result.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Block\Adminhtml\Log\Grid\Renderer;

class Result extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getResult() === null || $row->getResult() == 0) {
            return '<span class="grid-severity-major"><span>' . __('No Result') . '</span></span>';
        } else {
            if ($row->getResult() == 1) {
                return '<span class="grid-severity-notice"><span>' . __('Success') . '</span></span>';
            } else {
                if ($row->getResult() == 2) {
                    return '<span class="grid-severity-minor"><span>' . __('Warning') . '</span></span>';
                } else {
                    if ($row->getResult() == 3) {
                        return '<span class="grid-severity-critical"><span>' . __('Failed') . '</span></span>';
                    }
                }
            }
        }
        return '';
    }
}
