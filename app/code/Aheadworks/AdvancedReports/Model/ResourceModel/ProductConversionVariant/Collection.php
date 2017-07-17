<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\ProductConversionVariant;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\Conversion as ResourceConversion;
use Aheadworks\AdvancedReports\Model\ResourceModel\ProductConversion as ResourceProductConversion;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\ProductConversion
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * @var bool
     */
    protected $periodBased = true;

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceProductConversion::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->columns($this->getColumns(false))
            ->group('product_id');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $ordersCount = 'SUM(main_table.orders_count)';
        $viewsCount = 'SUM(main_table.views_count)';
        return [
            'period' => 'period',
            'product_id' => 'main_table.product_id',
            'views_count' => 'COALESCE(SUM(main_table.views_count), 0)',
            'orders_count' => 'COALESCE(SUM(main_table.orders_count), 0)',
            'conversion_rate' => 'IF(' . $viewsCount . ' > 0, ' .
                'IF(' . $viewsCount . ' < ' . $ordersCount . ', 100, ' .
                $ordersCount . ' / ' . $viewsCount . ' * 100), 0)',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'include_refunded_items') {
            if ((int)$condition['eq'] == 1) {
                return $this;
            } else {
                return $this->addExcludeRefundedItemsFilter();
            }
        }
        if ($field == 'periodFilter') {
            return $this->addGroupByFilter();
        }
        if ($field == 'product_id') {
            $condition = $condition['eq'];
            return $this->addProductFilter($condition['product_id']);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    protected function addExcludeRefundedItemsFilter()
    {
        $query = '(is_refunded = 0 OR is_refunded IS NULL)';
        $this->conditionsForGroupBy[] = [
            'field' => $query,
            'condition' => []
        ];
        return $this;
    }

    /**
     * Add product id filter to collection
     *
     * @param int $productId
     * @return $this
     */
    public function addProductFilter($productId)
    {
        $query = '(main_table.product_id = ' . $productId . ')';
        $this->conditionsForGroupBy[] = [
            'field' => $query,
            'condition' => []
        ];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function addGroupByDay($periodFrom, $periodTo, $compareFrom, $compareTo)
    {
        parent::addGroupByDay($periodFrom, $periodTo, $compareFrom, $compareTo);
        $this->getSelect()->order('date', self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function groupByTable($table, $periodFrom, $periodTo, $compareFrom, $compareTo)
    {
        parent::groupByTable($table, $periodFrom, $periodTo, $compareFrom, $compareTo);
        $this->getSelect()->order('start_date', self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrderStatuses()
    {
        $orderStatuses = parent::getOrderStatuses();
        $orderStatuses[] = ResourceConversion::VIEWED_STATUS;
        return $orderStatuses;
    }
}
