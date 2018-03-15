<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\SelectedFilters;

use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;

/**
 * Interface ItemRendererInterface
 * @package Aheadworks\Layerednav\Block\SelectedFilters
 */
interface ItemRendererInterface
{
    /**
     * Render selected filter item
     *
     * @param FilterItem $item
     * @return string
     */
    public function render(FilterItem $item);
}
