<?php

/**
 * Product:       Xtento_TrackingImport (2.3.6)
 * ID:            udfo4pHNxuS90BZUogqDpS6w1nZogQNAsyJKdEZfzKQ=
 * Packaged:      2018-02-26T09:10:55+00:00
 * Last Modified: 2016-03-13T19:40:23+00:00
 * File:          app/code/Xtento/TrackingImport/Model/System/Config/Source/Import/Processor.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\System\Config\Source\Import;

use Magento\Framework\Option\ArrayInterface;

/**
 * @codeCoverageIgnore
 */
class Processor implements ArrayInterface
{
    /**
     * @var \Xtento\TrackingImport\Model\Import
     */
    protected $importModel;

    /**
     * Entity constructor.
     *
     * @param \Xtento\TrackingImport\Model\Import $importModel
     */
    public function __construct(\Xtento\TrackingImport\Model\Import $importModel)
    {
        $this->importModel = $importModel;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->importModel->getProcessors();
    }
}
