<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

/**
 * Interface DataBuilderInterface
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
interface DataBuilderInterface
{
    /**
     * Add Item Data
     *
     * @param string $label
     * @param string $value
     * @param int $count
     * @return void
     */
    public function addItemData($label, $value, $count);

    /**
     * Get Items Data
     *
     * @return array
     */
    public function build();
}
