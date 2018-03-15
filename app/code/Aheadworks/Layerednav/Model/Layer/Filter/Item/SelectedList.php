<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;

/**
 * Class SelectedList
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class SelectedList
{
    /**
     * @var Layer
     */
    private $layer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var FilterItem[]
     */
    private $items;

    /**
     * @var array
     */
    private $duplicatedLabels;

    /**
     * @param LayerResolver $layerResolver
     * @param Config $config
     */
    public function __construct(
        LayerResolver $layerResolver,
        Config $config
    ) {
        $this->layer = $layerResolver->get();
        $this->config = $config;
    }

    /**
     * Get items
     *
     * @return FilterItem[]
     */
    public function getItems()
    {
        if (!$this->items) {
            $items = $this->layer->getState()->getFilters();
            foreach ($items as $item) {
                $itemFilter = $item->getFilter();
                if ($this->isPriceFromToFilterItem($item)) {
                    $this->items[] = $item;
                } else {
                    $filterValues = explode(',', $item->getValue());
                    /** @var FilterItem $filterItem */
                    foreach ($itemFilter->getItems() as $filterItem) {
                        if ($itemFilter->getRequestVar() == $filterItem->getFilter()->getRequestVar()
                            && false !== array_search($filterItem->getValue(), $filterValues)
                        ) {
                            $this->items[] = $filterItem;
                        }
                    }
                }
            }
        }
        return $this->items;
    }

    /**
     * Check if item with same label already exists
     *
     * @param FilterItem $item
     * @return bool
     */
    public function hasSame(FilterItem $item)
    {
        if (!$this->duplicatedLabels) {
            $labels = [];
            $this->duplicatedLabels[] = [];
            foreach ($this->getItems() as $filterItem) {
                $itemLabel = $this->getItemLabelForCompare($filterItem);
                if (!in_array($itemLabel, $labels)) {
                    $labels[] = $itemLabel;
                } else {
                    $this->duplicatedLabels[] = $itemLabel;
                }
            }
        }
        return in_array($item->getLabel(), $this->duplicatedLabels);
    }

    /**
     * Get item label for compare
     *
     * @param FilterItem $item
     * @return string
     */
    private function getItemLabelForCompare(FilterItem $item)
    {
        return $this->isPriceFromToFilterItem($item)
            ? $item->getValueString()
            : $item->getLabel();
    }

    /**
     * Check if filter item corresponds to price from-to filter
     *
     * @param FilterItem $item
     * @return bool
     */
    private function isPriceFromToFilterItem($item)
    {
        return $item->getFilter()->hasAttributeModel()
            && $item->getFilter()->getAttributeModel()->getAttributeCode() == 'price'
            && ($this->config->isPriceSliderEnabled() || $this->config->isPriceFromToEnabled());
    }
}
