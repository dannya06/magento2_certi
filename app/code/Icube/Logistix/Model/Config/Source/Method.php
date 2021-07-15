<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icube\Logistix\Model\Config\Source;

class Method
{
    public function toOptionArray()
    {
        return [
            ['value' => "JNT", 'label' => __('JNT')],
            ['value' => "POS", 'label' => __('POS')],
            ['value' => "JNE", 'label' => __('JNE')],
            ['value' => "GRAB", 'label' => __('GRAB')],
            ['value' => "GOSEND", 'label' => __('GOSEND')],
            ['value' => "SICEPAT", 'label' => __('SICEPAT')]
        ];
    }
}
