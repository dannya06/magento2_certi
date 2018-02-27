<?php

/**
 * Product:       Xtento_OrderExport (2.4.9)
 * ID:            kjiHrRgP31/ss2QGU3BYPdA4r7so/jI2cVx8SAyQFKw=
 * Packaged:      2018-02-26T09:11:23+00:00
 * Last Modified: 2017-11-27T20:04:32+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Settings/ConfigDataConverter.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Settings;

class ConfigDataConverter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $settings = [];
        foreach ($source->getElementsByTagName('setting') as $setting) {
            $name = $setting->getAttribute('name');
            $settings[$name] = $setting->nodeValue;
        }
        return $settings;
    }
}
