<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel;

use Aheadworks\AdvancedReports\Model\Config;
use Aheadworks\AdvancedReports\Model\Filter;

/**
 * Class AbstractCollection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**#@+
     * Period filter placeholders
     */
    const PERIOD_FROM_PLACEHOLDER = '%PERIOD_FROM%';
    const PERIOD_TO_PLACEHOLDER = '%PERIOD_TO%';
    /**#@-*/

    /**
     * @var string
     */
    const GROUP_TABLE_ALIAS = 'group_table';

    /**
     * @var string
     */
    protected $timeField = 'period';

    /**
     * @var bool
     */
    protected $periodBased = false;

    /**
     * @var bool
     */
    protected $topFilterForChart = false;

    /**
     * @var []
     */
    protected $conditionsForGroupBy = [];

    /**
     * @var []
     */
    protected $reportSettings = [];

    /**
     * @var bool
     */
    protected $compareMode = false;

    /**
     * @var []
     */
    protected $conditionsForPeriod = [];

    /**
     * @var AbstractCollection
     */
    protected $collectionSelect;

    /**
     * @var Filter\Store
     */
    protected $storeFilter;

    /**
     * @var Filter\CustomerGroup
     */
    protected $customerGroupFilter;

    /**
     * @var Filter\Groupby
     */
    protected $groupbyFilter;

    /**
     * @var Filter\Period
     */
    protected $periodFilter;

    /**
     * @var Config
     */
    private $config;

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
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->config = $config;
        $this->groupbyFilter = $groupbyFilter;
        $this->periodFilter = $periodFilter;
        $this->customerGroupFilter = $customerGroupFilter;
        $this->storeFilter = $storeFilter;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Add group filter to collection
     *
     * @return $this
     */
    public function addGroupByFilter()
    {
        $periodFrom = $this->periodFilter->getPeriodFrom();
        $periodTo = $this->periodFilter->getPeriodTo();
        $compareFrom = $this->periodFilter->getCompareFrom();
        $compareTo = $this->periodFilter->getCompareTo();
        switch($this->groupbyFilter->getCurrentGroupByKey()) {
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_DAY:
                $this->addGroupByDay($periodFrom, $periodTo, $compareFrom, $compareTo);
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_WEEK:
                $table = $this->getTable('aw_arep_weeks');
                $this->groupByTable($table, $periodFrom, $periodTo, $compareFrom, $compareTo);
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_MONTH:
                $table = $this->getTable('aw_arep_month');
                $this->groupByTable($table, $periodFrom, $periodTo, $compareFrom, $compareTo);
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_QUARTER:
                $table = $this->getTable('aw_arep_quarter');
                $this->groupByTable($table, $periodFrom, $periodTo, $compareFrom, $compareTo);
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_YEAR:
                $table = $this->getTable('aw_arep_year');
                $this->groupByTable($table, $periodFrom, $periodTo, $compareFrom, $compareTo);
                break;
        }
        return $this;
    }

    /**
     * Add period filter to collection
     *
     * @return $this
     */
    public function addPeriodFilter()
    {
        $from = $this->periodFilter->getPeriodFrom();
        $to = $this->periodFilter->getPeriodTo();
        $compareFrom = $this->periodFilter->getCompareFrom();
        $compareTo = $this->periodFilter->getCompareTo();

        $this->savePeriodFilterValues($from, $to, $compareFrom, $compareTo);
        $this->addFieldToFilter($this->timeField, [
            'from' => self::PERIOD_FROM_PLACEHOLDER,
            'to' => self::PERIOD_TO_PLACEHOLDER
        ]);
        return $this;
    }

    /**
     * Add order statuses filter to collection
     *
     * @return $this
     */
    public function addOrderStatusFilter()
    {
        $this->addFieldToFilter('order_status', ['in' => $this->getOrderStatuses()]);
        return $this;
    }

    /**
     * Add customer group filter to collection
     *
     * @return $this
     */
    public function addCustomerGroupFilter()
    {
        $customerGroupsId = $this->customerGroupFilter->getCustomerGroupId();
        if ($this->periodBased) {
            if (null != $customerGroupsId) {
                $this->conditionsForGroupBy[] = [
                    'field' => 'main_table.customer_group_id',
                    'condition' => ['in' => $customerGroupsId]
                ];
            }
        } else {
            if (null != $customerGroupsId) {
                $this->addFieldToFilter('customer_group_id', ['in' => $customerGroupsId]);
            }
        }
        return $this;
    }

    /**
     * Add store filter to collection
     *
     * @return $this
     */
    public function addStoreFilter()
    {
        $storeIds = $this->storeFilter->getStoreIds();
        if ($this->periodBased) {
            if (null != $storeIds) {
                $this->conditionsForGroupBy[] = [
                    'field' => 'main_table.store_id',
                    'condition' => ['in' => $storeIds]
                ];
            }
        } else {
            if (null != $storeIds) {
                $this->addFieldToFilter('store_id', ['in' => $storeIds]);
            }
        }
        return $this;
    }

    /**
     * Add filter to collection for chart
     *
     * @return $this
     */
    public function addFilterForChart()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'report_settings_order_status') {
            if ($condition['eq']) {
                $this->reportSettings[$field] = $condition['eq'];
            }
            return $this;
        }
        if ($field == 'customerGroupFilter') {
            $this->addCustomerGroupFilter();
            return $this;
        }
        if ($field == 'storeFilter') {
            return $this->addStoreFilter();
        }
        if ($field == 'periodFilter') {
            $this->addPeriodFilter();
            $this->addOrderStatusFilter();
            return $this;
        }
        if (
            $field == 'store_id' ||
            $field == 'customer_group_id' ||
            $field == 'order_status' ||
            $field == $this->timeField
        ) {
            return parent::addFieldToFilter($field, $condition);
        }
        // Apply filters for grid query
        return $this->addFilter($field, $condition, 'public');
    }

    /**
     * Retrieve totals
     *
     * @return []
     */
    public function getTotals()
    {
        $collectionSelect = clone $this->collectionSelect->getSelect();
        $collectionSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $totalSelect = clone $this->getSelect();
        $totalSelect->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->reset(\Magento\Framework\DB\Select::FROM)
            ->reset(\Magento\Framework\DB\Select::GROUP)
            ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $collectionSelect))],
                $this->getTotalColumns()
            );
        return $this->getConnection()->fetchRow($totalSelect) ?: [];
    }

    /**
     * Retrieve chart rows
     *
     * @return []
     */
    public function getChartRows()
    {
        $collectionSelect = clone $this->collectionSelect;

        $collectionSelect
            ->addFilterForChart();
        $collectionSelect->getSelect()
            ->reset(\Zend_Db_Select::LIMIT_COUNT)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET);

        $chartSelect = clone $this->getSelect();
        $chartSelect->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->reset(\Magento\Framework\DB\Select::FROM)
            ->reset(\Magento\Framework\DB\Select::GROUP)
            ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
            ->reset(\Magento\Framework\DB\Select::ORDER)
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $collectionSelect->getSelect()))],
                ['*']
            );
        if ($this->topFilterForChart) {
            $chartSelect->order('order_items_count ' . self::SORT_ORDER_DESC)
                ->limit(10);
        }

        return $this->getConnection()->fetchAll($chartSelect) ?: [];
    }

    /**
     * Retrieve report columns
     *
     * @param boolean $addRate
     * @return []
     */
    abstract protected function getColumns($addRate = false);

    /**
     * Retrieve report total columns
     *
     * @param boolean $addRate
     * @return []
     */
    protected function getTotalColumns($addRate = false)
    {
        return $this->getColumns($addRate);
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->collectionSelect = clone $this;
        $this->renderSelect($this->collectionSelect->getSelect());

        // Change select for apply grid filters
        $this->getSelect()->reset()->from(
            ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $this->collectionSelect->getSelect()))],
            ['*']
        );
        parent::_renderFiltersBefore();
    }

    /**
     * Change main table
     *
     * @param string $suffix
     * @return $this
     */
    protected function changeMainTable($suffix)
    {
        $this->setMainTable($this->getMainTable() . $suffix);
        return $this;
    }

    /**
     * Add group by day to collection
     *
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @param \DateTime $compareFrom
     * @param \DateTime $compareTo
     * @return $this
     */
    protected function addGroupByDay($periodFrom, $periodTo, $compareFrom, $compareTo)
    {
        $table = $this->getTable('aw_arep_days');
        if ($periodFrom && $periodTo) {
            $this->savePeriodFilterValues($periodFrom, $periodTo, $compareFrom, $compareTo);
            $this->getSelect()->where(
                '(' . self::GROUP_TABLE_ALIAS . '.date BETWEEN "' . self::PERIOD_FROM_PLACEHOLDER . '" AND "'
                . self::PERIOD_TO_PLACEHOLDER . '")'
            );
        }

        $this->getSelect()
            ->joinRight(
                [self::GROUP_TABLE_ALIAS => $table],
                $this->getConditionForGroupBy() . ' AND ' .
                $this->timeField . ' = ' . self::GROUP_TABLE_ALIAS . '.date AND ' .
                $this->_getConditionSql('main_table.order_status', ['in' => $this->getOrderStatuses()])
            );
        $this->getSelect()->group(self::GROUP_TABLE_ALIAS . '.date');
        return $this;
    }

    /**
     * Add group by table to collection
     *
     * @param string $table
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @param \DateTime $compareFrom
     * @param \DateTime $compareTo
     * @return $this
     */
    protected function groupByTable($table, $periodFrom, $periodTo, $compareFrom, $compareTo)
    {
        if ($periodFrom && $periodTo) {
            $this->savePeriodFilterValues($periodFrom, $periodTo, $compareFrom, $compareTo);
            $this->getSelect()->where(
                self::GROUP_TABLE_ALIAS . '.start_date <= "' . self::PERIOD_TO_PLACEHOLDER . '"'
            );
            $this->getSelect()->where(
                self::GROUP_TABLE_ALIAS . '.end_date >= "' . self::PERIOD_FROM_PLACEHOLDER . '"'
            );
        }
        $this->getSelect()
            ->joinRight(
                [self::GROUP_TABLE_ALIAS => $table],
                $this->getConditionForGroupBy() . ' AND ' .
                $this->timeField . ' >= "' . self::PERIOD_FROM_PLACEHOLDER . '" AND '
                . $this->timeField . ' <= "' . self::PERIOD_TO_PLACEHOLDER . '"'
                . ' AND ' . $this->timeField . ' BETWEEN group_table.start_date AND group_table.end_date '
                . ' AND ' . $this->_getConditionSql('main_table.order_status', ['in' => $this->getOrderStatuses()])
            )
            ->group(self::GROUP_TABLE_ALIAS . '.start_date');
        return $this;
    }

    /**
     * Retrieve rate field if necessary
     *
     * @param boolean $addRate
     * @return $string
     */
    protected function getRateField($addRate = true)
    {
        return (null === $this->storeFilter->getStoreIds() && $addRate) ? ' * main_table.to_global_rate' : '';
    }

    /**
     * Get condition for group by day, week, month, quarter, year
     *
     * @return string
     */
    protected function getConditionForGroupBy()
    {
        $joinCondition = '1=1';
        foreach ($this->conditionsForGroupBy as $condition) {
            $joinCondition .= ' AND ' . ($condition['condition']
                ? $this->_getConditionSql($condition['field'], $condition['condition'])
                : $condition['field']);
        }
        return $joinCondition;
    }

    /**
     * Retrieve order statuses from config
     *
     * @return []
     */
    protected function getOrderStatuses()
    {
        if (isset($this->reportSettings['report_settings_order_status'])) {
            return $this->reportSettings['report_settings_order_status'];
        }
        return explode(',', $this->config->getOrderStatus());
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'period') {
            switch($this->groupbyFilter->getCurrentGroupByKey()) {
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_DAY:
                    $field = 'date';
                    break;
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_WEEK:
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_MONTH:
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_QUARTER:
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_YEAR:
                    $field = 'start_date';
                    break;
            }
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * Enable compare mode
     *
     * @return void
     */
    public function enableCompareMode()
    {
        $this->compareMode = true;
    }

    /**
     * Save period filter values
     *
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @param \DateTime|null $compareFrom
     * @param \DateTime|null $compareTo
     * @return $this
     */
    protected function savePeriodFilterValues($periodFrom, $periodTo, $compareFrom = null, $compareTo = null)
    {
        $this->conditionsForPeriod['period_from'] = $periodFrom->format('Y-m-d');
        $this->conditionsForPeriod['period_to'] = $periodTo->format('Y-m-d');
        if ($compareFrom && $compareTo) {
            $this->conditionsForPeriod['compare_from'] = $compareFrom->format('Y-m-d');
            $this->conditionsForPeriod['compare_to'] = $compareTo->format('Y-m-d');
        }
        return $this;
    }

    /**
     * Render select
     *
     * @param \Zend_Db_Select $select
     * @return \Zend_Db_Select
     */
    protected function renderSelect($select)
    {
        if (
            $this->compareMode &&
            isset($this->conditionsForPeriod['compare_from']) &&
            isset($this->conditionsForPeriod['compare_to'])
        ) {
            $this->renderPeriodFilterValues(
                $select,
                $this->conditionsForPeriod['compare_from'],
                $this->conditionsForPeriod['compare_to']
            );
        } else if (
            isset($this->conditionsForPeriod['period_from']) &&
            isset($this->conditionsForPeriod['period_to'])
        ) {
            $this->renderPeriodFilterValues(
                $select,
                $this->conditionsForPeriod['period_from'],
                $this->conditionsForPeriod['period_to']
            );
        }
        return $select;
    }

    /**
     * Render period filter values
     *
     * @param \Zend_Db_Select $select
     * @param string $periodFrom
     * @param string $periodTo
     * @return \Zend_Db_Select
     */
    private function renderPeriodFilterValues($select, $periodFrom, $periodTo)
    {
        $where = $select->getPart(\Zend_Db_Select::WHERE);
        foreach ($where as $key => $value) {
            $where[$key] = str_replace(
                self::PERIOD_FROM_PLACEHOLDER,
                $periodFrom,
                str_replace(
                    self::PERIOD_TO_PLACEHOLDER,
                    $periodTo,
                    $value
                )
            );
        }
        $select->setPart(\Zend_Db_Select::WHERE, $where);

        $from = $select->getPart(\Zend_Db_Select::FROM);
        foreach ($from as $key => $value) {
            if (isset($value['joinCondition']) && $value['joinCondition']) {
                $from[$key]['joinCondition'] = str_replace(
                    self::PERIOD_FROM_PLACEHOLDER,
                    $periodFrom,
                    str_replace(
                        self::PERIOD_TO_PLACEHOLDER,
                        $periodTo,
                        $value['joinCondition']
                    )
                );
            }
        }
        $select->setPart(\Zend_Db_Select::FROM, $from);

        return $select;
    }
}
