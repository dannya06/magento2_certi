<?php

/**
 * Product:       Xtento_TrackingImport (2.3.6)
 * ID:            udfo4pHNxuS90BZUogqDpS6w1nZogQNAsyJKdEZfzKQ=
 * Packaged:      2018-02-26T09:10:55+00:00
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