<?php

/**
 * Product:       Xtento_OrderExport (2.3.7)
 * ID:            vuwMiuqT6hJFCgwIsMBM7iJwY9/E3ScMI/mHOqvUFvQ=
 * Packaged:      2017-10-04T08:30:08+00:00
 * Last Modified: 2016-03-02T15:34:05+00:00
 * File:          app/code/Xtento/OrderExport/Block/Adminhtml/Destination/Edit/Tab/Type/Sftp.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Block\Adminhtml\Destination\Edit\Tab\Type;

class Sftp extends Ftp
{
    // SFTP Configuration
    public function getFields(\Magento\Framework\Data\Form $form, $type = 'SFTP')
    {
        parent::getFields($form, $type);
    }
}