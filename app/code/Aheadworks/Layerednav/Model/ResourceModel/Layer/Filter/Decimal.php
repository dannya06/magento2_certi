<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Decimal
 * @package Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter
 */
class Decimal extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('catalog_product_index_eav_decimal', 'entity_id');
    }

    /**
     * Retrieve array with products counts per range
     *
     * @param FilterInterface $filter
     * @param int $range
     * @return array
     */
    public function getCount(FilterInterface $filter, $range)
    {
        return $this->fetchCount(
            $this->getSelect($filter),
            $range,
            $filter->getAttributeModel()->getAttributeCode()
        );
    }

    /**
     * @param FilterInterface $filter
     * @param int $range
     * @return array
     */
    public function getParentCount(FilterInterface $filter, $range)
    {
        return $this->fetchCount(
            $this->getParentSelect($filter),
            $range,
            $filter->getAttributeModel()->getAttributeCode()
        );
    }

    /**
     * Retrieve product counts for a range
     *
     * @param Select $select
     * @param int $range
     * @param string $attributeCode
     * @return array
     */
    private function fetchCount(Select $select, $range, $attributeCode)
    {
        $connection = $this->getConnection();

        $tableAlias = sprintf('%s_idx', $attributeCode);
        $countExpr = new \Zend_Db_Expr('COUNT(*)');
        $rangeExpr = new \Zend_Db_Expr('FLOOR(' . $tableAlias . '.value / ' . $range . ') + 1');

        $select->columns(['decimal_range' => $rangeExpr, 'count' => $countExpr])
            ->group($rangeExpr);
        return $connection->fetchPairs($select);
    }

    /**
     * Retrieve clean select with joined index table
     * Joined table has index
     *
     * @param FilterInterface $filter
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return Select
     */
    private function getSelect(FilterInterface $filter)
    {
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        return $this->prepareSelectForCount($select, $filter, false);
    }

    /**
     * @param FilterInterface $filter
     * @return Select
     */
    private function getParentSelect(FilterInterface $filter)
    {
        // Clone select from collection with filters
        $select = clone $filter->getLayer()
            ->getProductCollection()
            ->getSelect();
        $select->reset(Select::WHERE);
        return $this->prepareSelectForCount($select, $filter, true);
    }

    /**
     * Prepare select to get count
     *
     * @param Select $select
     * @param FilterInterface $filter
     * @param bool $parent
     * @return Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function prepareSelectForCount(Select $select, FilterInterface $filter, $parent)
    {
        $attribute = $filter->getAttributeModel();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());

        $fromAndJoins = $this->prepereCountFromAndJoins($select, $filter, $tableAlias);
        $whereConditions = $parent
            ? null
            : $this->prepereCountWhereConditions($select, $tableAlias);

        // Reset columns, order and limitation conditions
        $select
            ->reset(Select::FROM)
            ->reset(Select::COLUMNS)
            ->reset(Select::ORDER)
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET)
            ->setPart(Select::FROM, $fromAndJoins); // Set part

        if ($whereConditions) {
            $where[] = implode(' AND ', $whereConditions);
            $select
                ->reset(Select::WHERE)
                ->setPart(Select::WHERE, $where);
        }
        return $select;
    }

    /**
     * Prepere count from and joins
     *
     * @param Select $select
     * @param FilterInterface $filter
     * @param string $tableAlias
     * @return array
     */
    private function prepereCountFromAndJoins($select, $filter, $tableAlias)
    {
        $connection = $this->getConnection();
        $attribute = $filter->getAttributeModel();
        $conditions = [
            $tableAlias . '.entity_id = e.entity_id',
            $connection->quoteInto($tableAlias . '.attribute_id = ?', $attribute->getAttributeId()),
            $connection->quoteInto($tableAlias . '.store_id = ?', $filter->getStoreId()),
        ];

        $newFromAndJoins = [];
        $fromAndJoins = $select->getPart(Select::FROM);
        foreach ($fromAndJoins as $key => $join) {
            if ($join['joinType'] == Select::FROM) {
                $newFromAndJoins[$tableAlias] = [
                    'joinType'      => Select::FROM,
                    'schema'        => $join['schema'],
                    'tableName'     => $this->getTable('catalog_product_index_eav_decimal'),
                    'joinCondition' => null
                ];
                if (isset($fromAndJoins['search_result'])) {
                    $newFromAndJoins['search_result'] = [
                        'joinType' => Select::INNER_JOIN,
                        'schema' => $join['schema'],
                        'tableName' => $fromAndJoins['search_result']['tableName'],
                        'joinCondition' => $tableAlias . '.entity_id = search_result.entity_id'
                    ];
                }
                $newFromAndJoins[$key] = [
                    'joinType'      => Select::LEFT_JOIN,
                    'schema'        => $join['schema'],
                    'tableName'     => $join['tableName'],
                    'joinCondition' => join(' AND ', $conditions)
                ];
                continue;
            }
            if ($key == $tableAlias || $key == 'search_result') {
                continue;
            }
            $newFromAndJoins[$key] = $join;
        }

        return $newFromAndJoins;
    }

    /**
     * Prepere count where conditions
     *
     * @param Select $select
     * @param string $tableAlias
     * @return array
     */
    private function prepereCountWhereConditions($select, $tableAlias)
    {
        $newWhereConditions = [];
        $whereConditions = $select->getPart(Select::WHERE);
        foreach ($whereConditions as $key => $condition) {
            if (false !== strpos($condition, $tableAlias)) {
                continue;
            }
            if (0 === strpos($condition, 'AND ')) {
                $condition = preg_replace('/AND /', '', $condition, 1);
            }
            $newWhereConditions[$key] = $condition;
        }

        return $newWhereConditions;
    }

    /**
     * @param FilterInterface $filter
     * @return int
     */
    public function getMaxValue(FilterInterface $filter)
    {
        $select = $this->getParentSelect($filter);
        $connection = $this->getConnection();

        $tableAlias = sprintf('%s_idx', $filter->getAttributeModel()->getAttributeCode());
        $select->columns(new \Zend_Db_Expr('MAX(' . $tableAlias . '.value)'));

        return (int)$connection->fetchOne($select);
    }

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function joinFilterToCollection(FilterInterface $filter)
    {
        $connection = $this->getConnection();
        $collection = $filter->getLayer()->getProductCollection();
        $attribute = $filter->getAttributeModel();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());

        $flag = 'attribute_joined_' . $tableAlias;
        if (!$collection->getFlag($flag)) {
            $conditions = [
                $tableAlias . '.entity_id = e.entity_id',
                $connection->quoteInto($tableAlias . '.attribute_id = ?', $attribute->getAttributeId()),
                $connection->quoteInto($tableAlias . '.store_id = ?', $collection->getStoreId()),
            ];

            $collection->getSelect()->joinLeft(
                [$tableAlias => $this->getMainTable()],
                implode(' AND ', $conditions),
                []
            );
            $collection->setFlag($flag, true);
        }

        return $this;
    }

    /**
     * Get where conditions
     *
     * @param FilterInterface $filter
     * @param array $intervals
     * @return array
     */
    public function getWhereConditions(FilterInterface $filter, $intervals = [])
    {
        $whereConditions = [];

        $attribute = $filter->getAttributeModel();
        $connection = $this->getConnection();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
        $currencyRate = $filter->getLayer()->getProductCollection()->getCurrencyRate();

        foreach ($intervals as $interval) {
            list($from, $to) = $interval;
            if ($from === '' && $to === '') {
                return $whereConditions;
            }
            $conditions = [];
            if ($from !== '') {
                $conditions[] = $connection->quoteInto(
                    $tableAlias . '.value >= ?',
                    $this->getBaseValue($from, $currencyRate)
                );
            }
            if ($to !== '') {
                $conditions[] = $connection->quoteInto(
                    $tableAlias . '.value < ?',
                    $this->getBaseValue($to, $currencyRate)
                );
            }

            $whereConditions[] = '(' . implode(' AND ', $conditions) . ')';
        }

        return $whereConditions;
    }

    /**
     * Get base value
     *
     * @param float $value
     * @param float $currencyRate
     * @return float|int
     */
    private function getBaseValue($value, $currencyRate)
    {
        return $value / $currencyRate;
    }
}
