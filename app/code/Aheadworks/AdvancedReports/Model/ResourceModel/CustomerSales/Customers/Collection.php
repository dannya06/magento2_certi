<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales\Customers;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales as ResourceCustomerSales;
use Aheadworks\AdvancedReports\Model\Config;
use Aheadworks\AdvancedReports\Model\Filter;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales\Customers
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var Filter\Range
     */
    private $rangeFilter;

    /**
     * @var []
     */
    private $conditionsForSales = [];

    /**
     * @var boolean
     */
    private $excludeRefunded;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Config $config
     * @param Filter\Store $storeFilter
     * @param Filter\CustomerGroup $customerGroupFilter
     * @param Filter\Groupby $groupbyFilter
     * @param Filter\Period $periodFilter
     * @param Filter\Range $rangeFilter
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Config $config,
        Filter\Store $storeFilter,
        Filter\CustomerGroup $customerGroupFilter,
        Filter\Groupby $groupbyFilter,
        Filter\Period $periodFilter,
        Filter\Range $rangeFilter,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->rangeFilter = $rangeFilter;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $config,
            $storeFilter,
            $customerGroupFilter,
            $groupbyFilter,
            $periodFilter,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceCustomerSales::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->columns($this->getColumns(true))
            ->group([
                'main_table.customer_id',
                'main_table.customer_email'
            ])
        ;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false, $excludeRefunded = false)
    {
        $rateField = $this->getRateField($addRate);

        if ($excludeRefunded) {
            $orderItemsCount = 'SUM(COALESCE(main_table.qty_ordered - main_table.qty_refunded, 0))';
            $total = 'SUM(COALESCE((main_table.total - main_table.total_refunded)' . $rateField . ', 0))';
            $orderItemsCountLifetime = 'COALESCE(lifetime_sales.qty_ordered - lifetime_sales.qty_refunded, 0)';
            $totalLifetime = 'COALESCE((lifetime_sales.total - lifetime_sales.total_refunded)' .
                $rateField . ', 0)';
        } else {
            $orderItemsCount = 'SUM(COALESCE(main_table.qty_ordered, 0))';
            $total = 'SUM(COALESCE(main_table.total' . $rateField . ', 0))';
            $orderItemsCountLifetime = 'COALESCE(lifetime_sales.qty_ordered, 0)';
            $totalLifetime = 'COALESCE(lifetime_sales.total' . $rateField . ', 0)';
        }
        return [
            'customer_id' => "main_table.customer_id",
            'customer_name' => "main_table.customer_name",
            'customer_email' => "main_table.customer_email",
            'customer_group' => "main_table.customer_group",
            'country' => "main_table.country",
            'region' => "main_table.region",
            'phone' => "main_table.phone",
            'total_sales_for_period' => $total,
            'orders_count_for_period' => 'SUM(COALESCE(main_table.orders_count, 0))',
            'order_items_count_for_period' => $orderItemsCount,
            'total_sales_lifetime' => $totalLifetime,
            'orders_count_lifetime' => 'COALESCE(lifetime_sales.orders_count, 0)',
            'order_items_lifetime' => $orderItemsCountLifetime,
            'created_in' => "main_table.created_in",
            'last_login_at' => "main_table.last_login_at",
            'last_order_at' => "main_table.last_order_at",
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTotalColumns($addRate = false)
    {
        return [
            'customer_name' => "main_table.customer_name",
            'customer_email' => "main_table.customer_email",
            'customer_group' => "main_table.customer_group",
            'country' => "main_table.country",
            'region' => "main_table.region",
            'phone' => "main_table.phone",
            'total_sales_for_period' => 'SUM(COALESCE(main_table.total_sales_for_period, 0))',
            'orders_count_for_period' => 'SUM(COALESCE(main_table.orders_count_for_period, 0))',
            'order_items_count_for_period' => 'SUM(COALESCE(main_table.order_items_count_for_period, 0))',
            'total_sales_lifetime' => 'SUM(COALESCE(total_sales_lifetime, 0))',
            'orders_count_lifetime' => 'SUM(COALESCE(orders_count_lifetime, 0))',
            'order_items_lifetime' => 'SUM(COALESCE(order_items_lifetime, 0))',
            'created_in' => "main_table.created_in",
            'last_login_at' => "main_table.last_login_at",
            'last_order_at' => "main_table.last_order_at",
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'exclude_refunded') {
            if ((int)$condition['eq'] == 1) {
                return $this->addExcludeRefundedItemsFilter();
            } else {
                return $this;
            }
        }
        if ($field == 'rangeFilter') {
            return $this->addRangeFilter();
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add exclude refunded items filters to collection
     *
     * @return $this
     */
    private function addExcludeRefundedItemsFilter()
    {
        $this->excludeRefunded = true;
        $this->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns($this->getColumns(true, true))
            ->where('? > 0', new \Zend_Db_Expr('(main_table.qty_ordered - main_table.qty_refunded)'));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    private function addRangeFilter()
    {
        $range = $this->rangeFilter->getRange();
        if (null != $range) {
            if (isset($range['from']) && $range['from']) {
                $this->addFieldToFilter('total_sales_for_period', ['gteq' => $range['from']]);
            }
            if (isset($range['to']) && $range['to']) {
                $this->addFieldToFilter('total_sales_for_period', ['lteq' => $range['to']]);
            }
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderStatusFilter()
    {
        $this->conditionsForSales[] = [
            'field' => 'order_status',
            'condition' => ['in' => $this->getOrderStatuses()]
        ];
        return parent::addOrderStatusFilter();
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomerGroupFilter()
    {
        $customerGroupsId = $this->customerGroupFilter->getCustomerGroupId();
        if (null != $customerGroupsId) {
            $this->conditionsForSales[] = [
                'field' => 'customer_group_id',
                'condition' => ['in' => $customerGroupsId]
            ];
        }
        return parent::addCustomerGroupFilter();
    }

    /**
     * {@inheritdoc}
     */
    public function addStoreFilter()
    {
        $storeIds = $this->storeFilter->getStoreIds();
        if (null != $storeIds) {
            $this->conditionsForSales[] = [
                'field' => 'store_id',
                'condition' => ['in' => $storeIds]
            ];
            $this->getSelect()
                ->where('main_table.store_id in (?)', $storeIds);
        }
        return $this;
    }

    /**
     * Get condition for lifetime sales
     *
     * @return string
     */
    protected function getConditionForSales()
    {
        $joinCondition = '1=1';
        foreach ($this->conditionsForSales as $condition) {
            $joinCondition .= ' AND ' . ($condition['condition']
                    ? $this->_getConditionSql($condition['field'], $condition['condition'])
                    : $condition['field']);
        }
        return $joinCondition;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $excludeRefunded = '1=1';
        if ($this->excludeRefunded) {
            $excludeRefunded = '((qty_ordered - qty_refunded) > 0)';
        }
        $this->getSelect()
            ->joinRight(
                ['lifetime_sales' => new \Zend_Db_Expr(
                    '(SELECT customer_id, customer_email, SUM(orders_count) as orders_count, 
                    SUM(qty_ordered) as qty_ordered, SUM(qty_refunded) as qty_refunded, SUM(total) as total, 
                    SUM(total_refunded) as total_refunded
                    FROM ' . $this->getMainTable() . ' WHERE ' . $this->getConditionForSales() .
                    ' AND ' . $excludeRefunded . ' GROUP BY customer_id, customer_email)'
                )],
                'IF(main_table.customer_id IS NULL, 
                lifetime_sales.customer_email = main_table.customer_email AND lifetime_sales.customer_id IS NULL,
                lifetime_sales.customer_id = main_table.customer_id)',
                []
            );
        parent::_renderFiltersBefore();
    }
}
