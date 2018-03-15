<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\Navigation;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\LayeredNavigation\Block\Navigation\FilterRendererInterface;
use Aheadworks\Layerednav\Model\Config;

/**
 * Class FilterRenderer
 * @package Aheadworks\Layerednav\Block\Navigation\FilterRenderer
 */
class FilterRenderer extends Template implements FilterRendererInterface
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
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layer = $layerResolver->get();
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function render(FilterInterface $filter)
    {
        $this->assign('filterItems', $filter->getItems());
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }

    /**
     * Check if filter item is active
     *
     * @param FilterItem $item
     * @return bool
     * @throws LocalizedException
     */
    public function isActiveItem(FilterItem $item)
    {
        foreach ($this->layer->getState()->getFilters() as $filter) {
            if ($filter->getFilter()->getRequestVar() == $item->getFilter()->getRequestVar()) {
                $filterValues = explode(',', $filter->getValue());
                if (false !== array_search($item->getValue(), $filterValues)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get attribute filter backend type. Returns '' for non-attribute filter items
     *
     * @param FilterItem $item
     * @return string
     * @throws LocalizedException
     */
    public function getBackendType(FilterItem $item)
    {
        $filter = $item->getFilter();
        if ($filter->hasData('attribute_model')) {
            return $filter->getAttributeModel()->getBackendType();
        }
        return '';
    }

    /**
     * Check if need to show products count in the parentheses
     *
     * @param FilterItem $item
     * @return bool
     */
    public function isNeedToShowProductsCount(FilterItem $item)
    {
        return $this->config->isNeedToShowProductsCount()
            && (!$this->isActiveItem($item))
            && $item->getCount();
    }

    /**
     * Check if need to to show item
     *
     * @param FilterItem $item
     * @return bool
     */
    public function isNeedToShowItem(FilterItem $item)
    {
        return !$this->config->hideEmptyAttributeValues()
            || ($this->isActiveItem($item))
            || ($item->getCount());
    }

    /**
     * Get filter items count to display
     *
     * @param FilterItem[] $filterItems
     * @return int
     */
    private function getDisplayItemsCount($filterItems)
    {
        $count = 0;
        foreach ($filterItems as $filterItem) {
            if ($this->isNeedToShowItem($filterItem)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get count of show more items
     *
     * @param FilterItem[] $filterItems
     * @return int
     */
    public function getShowMoreCount($filterItems)
    {
        $displayLimit = $this->config->getFilterValuesDisplayLimit();
        $itemsCount = $this->getDisplayItemsCount($filterItems);
        if (isset($displayLimit) && $displayLimit > 0 && $itemsCount > $displayLimit) {
            return $itemsCount - $displayLimit;
        }
        return 0;
    }

    /**
     * Get filter values display limit
     *
     * @return int
     */
    public function getDisplayLimit()
    {
        return $this->config->getFilterValuesDisplayLimit();
    }
}
