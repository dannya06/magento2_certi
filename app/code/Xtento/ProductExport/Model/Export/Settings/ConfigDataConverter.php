<?php

/**
 * Product:       Xtento_ProductExport (2.5.0)
 * ID:            cb9PRAWlxmJOwg/jsj5X3dDv0+dPZORkauC/n26ZNAU=
 * Packaged:      2018-02-26T09:11:39+00:00
 * Last Modified: 2017-11-28T11:25:41+00:00
 * File:          app/code/Xtento/ProductExport/Model/Export/Settings/ConfigDataConverter.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Model\Export\Settings;

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
