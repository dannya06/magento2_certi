<?php

/**
 * Product:       Xtento_TrackingImport (2.3.6)
 * ID:            udfo4pHNxuS90BZUogqDpS6w1nZogQNAsyJKdEZfzKQ=
 * Packaged:      2018-02-26T09:10:55+00:00
 * Last Modified: 2016-03-13T19:40:19+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Source/Grid/Renderer/Result.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Source\Grid\Renderer;

class Result extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render source status
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $class = 'grid-severity-major';
        $text = __('No Result');
        switch ($this->_getValue($row)) {
            case 0:
                $class = 'grid-severity-critical';
                $text = __('Failed');
                break;
            case 1:
                $class = 'grid-severity-notice';
                $text = __('Success');
                break;
        }
        return '<span class="' . $class . '"><span>' . $text . '</span></span>';
    }
}
