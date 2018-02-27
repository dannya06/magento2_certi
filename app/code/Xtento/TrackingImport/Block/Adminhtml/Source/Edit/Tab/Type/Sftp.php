<?php

/**
 * Product:       Xtento_TrackingImport (2.3.6)
 * ID:            udfo4pHNxuS90BZUogqDpS6w1nZogQNAsyJKdEZfzKQ=
 * Packaged:      2018-02-26T09:10:55+00:00
 * Last Modified: 2016-03-11T17:40:19+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Source/Edit/Tab/Type/Sftp.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Source\Edit\Tab\Type;

class Sftp extends Ftp
{
    // SFTP Configuration
    public function getFields(\Magento\Framework\Data\Form $form, $type = 'SFTP')
    {
        parent::getFields($form, $type);
    }
}