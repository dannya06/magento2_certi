<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResource;
use Aheadworks\Layerednav\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter
 * @codeCoverageIgnore
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Filter::class, FilterResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            'aw_layerednav_filter_title',
            'id',
            'filter_id',
            ['store_id', 'value'],
            FilterInterface::STOREFRONT_TITLES
        );
        $this->attachRelationTable(
            'aw_layerednav_filter_display_state',
            'id',
            'filter_id',
            ['store_id', 'value'],
            FilterInterface::DISPLAY_STATES
        );
        $this->attachRelationTable(
            'aw_layerednav_filter_sort_order',
            'id',
            'filter_id',
            ['store_id', 'value'],
            FilterInterface::SORT_ORDERS
        );
        $this->attachRelationTable(
            'aw_layerednav_filter_exclude_category',
            'id',
            'filter_id',
            'category_id',
            FilterInterface::EXCLUDE_CATEGORY_IDS
        );

        /** @var \Magento\Framework\DataObject $item */
        foreach ($this as $item) {
            $title = $this->getStorefrontValue($item->getData(FilterInterface::STOREFRONT_TITLES), true);
            $item->setData(
                FilterInterface::STOREFRONT_TITLE,
                $title ? $title : $item->getData(FilterInterface::DEFAULT_TITLE)
            );
            $item->setData(
                FilterInterface::STOREFRONT_DISPLAY_STATE,
                $this->getStorefrontValue($item->getData(FilterInterface::DISPLAY_STATES), true)
            );
            $item->setData(
                FilterInterface::STOREFRONT_SORT_ORDER,
                $this->getStorefrontValue($item->getData(FilterInterface::SORT_ORDERS), true)
            );
        }

        return parent::_afterLoad();
    }

    /**
     * Add filter by code
     *
     * @param string $code
     * @return $this
     */
    public function addFilterByCode($code = '')
    {
        if ($code) {
            $this->addFieldToFilter(FilterInterface::CODE, ['eq' => $code]);
        }
        return $this;
    }

    /**
     * Add filter by type
     *
     * @param string $type
     * @return $this
     */
    public function addFilterByType($type = '')
    {
        if ($type) {
            $this->addFieldToFilter(FilterInterface::TYPE, ['eq' => $type]);
        }
        return $this;
    }
}
