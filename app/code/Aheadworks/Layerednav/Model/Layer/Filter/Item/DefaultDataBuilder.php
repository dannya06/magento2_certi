<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder as ItemDataBuilder;

/**
 * Class DefaultDataBuilder
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class DefaultDataBuilder extends ItemDataBuilder implements DataBuilderInterface
{
    /**
     * @return array
     */
    public function build()
    {
        $result = $this->_itemsData;
        $this->_itemsData = [];
        return $result;
    }
}
