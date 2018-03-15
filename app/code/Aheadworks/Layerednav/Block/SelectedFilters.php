<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block;

use Aheadworks\Layerednav\Block\SelectedFilters\ItemRendererInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\SelectedList as ItemsList;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class SelectedFilters
 *
 * @method ItemRendererInterface[] getItemRenders()
 *
 * @package Aheadworks\Layerednav\Block
 */
class SelectedFilters extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ItemsList
     */
    private $itemsList;

    /**
     * @var ItemRendererInterface[]
     */
    private $itemRenders = [];

    /**
     * @param Context $context
     * @param Config $config
     * @param ItemsList $itemsList
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        ItemsList $itemsList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->itemsList = $itemsList;
    }

    /**
     * Get active filter items
     *
     * @return FilterItem[]
     */
    public function getActiveFilterItems()
    {
        return $this->itemsList->getItems();
    }

    /**
     * Get item renderer
     *
     * @param FilterItem $item
     * @return ItemRendererInterface
     * @throws \Exception
     */
    public function getItemRenderer(FilterItem $item)
    {
        $rendererType = $this->isPriceFromToFilterItem($item)
            ? 'price-from-to'
            : 'default';
        if (!isset($this->itemRenders[$rendererType])) {
            $itemRenderClasses = $this->getItemRenders();
            if (!isset($itemRenderClasses[$rendererType])) {
                throw new \Exception(sprintf('Unknown item renderer type: %s requested', $rendererType));
            }
            $rendererInstance = $this->getLayout()->createBlock($itemRenderClasses[$rendererType]);
            if (!$rendererInstance instanceof ItemRendererInterface) {
                throw new \Exception(
                    sprintf('Item renderer instance %s does not implement required interface.', $rendererType)
                );
            }
            $this->itemRenders[$rendererType] = $rendererInstance;
        }
        return $this->itemRenders[$rendererType];
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

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $filterItems = $this->getActiveFilterItems();
        if (!count($filterItems)) {
            return '';
        }
        return parent::toHtml();
    }
}
