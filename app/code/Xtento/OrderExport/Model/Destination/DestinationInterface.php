<?php

/**
 * Product:       Xtento_OrderExport (2.4.9)
 * ID:            kjiHrRgP31/ss2QGU3BYPdA4r7so/jI2cVx8SAyQFKw=
 * Packaged:      2018-02-26T09:11:23+00:00
 * Last Modified: 2015-08-10T10:20:49+00:00
 * File:          app/code/Xtento/OrderExport/Model/Destination/DestinationInterface.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Destination;

interface DestinationInterface
{
    public function testConnection();
    public function saveFiles($fileArray);
}