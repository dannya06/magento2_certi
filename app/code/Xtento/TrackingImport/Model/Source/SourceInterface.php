<?php

/**
 * Product:       Xtento_TrackingImport (2.3.0)
 * ID:            HdWKOY0KdgGaRx+26HyONH06+SvSVZH7A2yQmSKRHJU=
 * Packaged:      2017-10-04T08:30:20+00:00
 * Last Modified: 2016-03-13T19:37:15+00:00
 * File:          app/code/Xtento/TrackingImport/Model/Source/SourceInterface.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\Source;

interface SourceInterface
{
    public function testConnection();

    public function loadFiles();

    public function archiveFiles($filesToProcess, $forceDelete = false);
}