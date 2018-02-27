<?php

/**
 * Product:       Xtento_OrderExport (2.4.9)
 * ID:            kjiHrRgP31/ss2QGU3BYPdA4r7so/jI2cVx8SAyQFKw=
 * Packaged:      2018-02-26T09:11:23+00:00
 * Last Modified: 2015-08-09T14:37:03+00:00
 * File:          app/code/Xtento/OrderExport/Model/System/Config/Source/Destination/Type.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\System\Config\Source\Destination;

use Magento\Framework\Option\ArrayInterface;

/**
 * @codeCoverageIgnore
 */
class Type implements ArrayInterface
{
    /**
     * @var \Xtento\OrderExport\Model\Destination
     */
    protected $destinationModel;

    /**
     * @param \Xtento\OrderExport\Model\Destination $destinationModel
     */
    public function __construct(\Xtento\OrderExport\Model\Destination $destinationModel)
    {
        $this->destinationModel = $destinationModel;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->destinationModel->getTypes();
    }

    public function getName($type) {
        foreach ($this->toOptionArray() as $optionType => $name) {
            if ($optionType == $type) {
                return $name;
            }
        }
        return '';
    }
}
