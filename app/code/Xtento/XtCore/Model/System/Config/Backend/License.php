<?php

/**
 * Product:       Xtento_XtCore (2.0.9)
 * ID:            vuwMiuqT6hJFCgwIsMBM7iJwY9/E3ScMI/mHOqvUFvQ=
 * Packaged:      2017-10-04T08:30:08+00:00
 * Last Modified: 2017-08-16T08:52:13+00:00
 * File:          app/code/Xtento/XtCore/Model/System/Config/Backend/License.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Model\System\Config\Backend;

class License extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        $this->_registry->register('xtento_configuration_license_key', $this->getValue(), true);
    }
}
