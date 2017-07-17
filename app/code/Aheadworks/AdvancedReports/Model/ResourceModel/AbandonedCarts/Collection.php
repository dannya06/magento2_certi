<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\AbandonedCarts;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\AbandonedCarts as ResourceAbandonedCarts;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\AbandonedCarts
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
     * @var bool
     */
    protected $periodBased = true;

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceAbandonedCarts::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->columns($this->getColumns(true))
        ;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $totalCarts = "SUM(COALESCE(main_table.total_carts, 0))";
        $abandonedCarts = "SUM(COALESCE(main_table.abandoned_carts, 0))";
        $rateField = $this->getRateField($addRate);
        return [
            'period'                => 'period',
            'total_carts'           => $totalCarts,
            'completed_carts'       => "SUM(COALESCE(main_table.completed_carts, 0))",
            'abandoned_carts'       => $abandonedCarts,
            'abandoned_carts_total' => "SUM(COALESCE(main_table.abandoned_carts_total" . $rateField . ", 0))",
            'abandonment_rate'      => "COALESCE((100 / " . $totalCarts . ") * " . $abandonedCarts . ", 0)",
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'periodFilter') {
            return $this->addGroupByFilter();
        }
        return parent::addFieldToFilter($field, $condition);
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
                $this->timeField . ' = ' . self::GROUP_TABLE_ALIAS . '.date'
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
                . ' AND ' . $this->timeField . ' BETWEEN group_table.start_date AND group_table.end_date'
            )
            ->group(self::GROUP_TABLE_ALIAS . '.start_date');
        return $this;
    }
}
