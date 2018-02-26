<?php

/**
 * Product:       Xtento_OrderExport (2.4.9)
 * ID:            kjiHrRgP31/ss2QGU3BYPdA4r7so/jI2cVx8SAyQFKw=
 * Packaged:      2018-02-26T09:11:23+00:00
 * Last Modified: 2017-11-13T17:32:12+00:00
 * File:          app/code/Xtento/OrderExport/Plugin/ExcludeFilesFromMinification.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Plugin;

use Magento\Framework\View\Asset\Minification;

class ExcludeFilesFromMinification
{
    public function aroundGetExcludes(Minification $subject, callable $proceed, $contentType)
    {
        $result = $proceed($contentType);
        if ($contentType != 'js') {
            return $result;
        }
        $result[] = 'Xtento_OrderExport/js/ace/mode-xml';
        $result[] = 'Xtento_OrderExport/js/ace/theme-eclipse';
        return $result;
    }
}