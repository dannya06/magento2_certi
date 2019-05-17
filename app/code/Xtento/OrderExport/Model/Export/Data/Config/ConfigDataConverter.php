<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            MlbKB4xzfXDFlN04cZrwR1LbEaw8WMlnyA9rcd7bvA8=
 * Last Modified: 2017-11-27T20:02:21+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Data/Config/ConfigDataConverter.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Data\Config;

class ConfigDataConverter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $classes = [];
        foreach ($source->getElementsByTagName('export') as $exportClass) {
            $id = $exportClass->getAttribute('id');
            $classes[$id] = [
                'class' => $exportClass->getAttribute('class'),
                'profile_ids' => !empty($exportClass->getAttribute('profile_ids')) ? $exportClass->getAttribute('profile_ids') : false
            ];
        }
        return [
            'classes' => $classes,
        ];
    }
}
