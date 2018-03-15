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
 * Class Attribute
 * @package Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter
 */
class Attribute extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('catalog_product_index_eav', 'entity_id');
    }

    /**
     * @param FilterInterface $filter
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function joinFilterToCollection(FilterInterface $filter)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $attribute = $filter->getAttributeModel();
        $connection = $this->getConnection();
        $tableAlias = $attribute->getAttributeCode() . '_idx';
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
     * Get where condition
     *
     * @param FilterInterface $filter
     * @param string|array $value
     * @return array
     */
    public function getWhereConditions(FilterInterface $filter, $value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        $attribute = $filter->getAttributeModel();
        $connection = $this->getConnection();
        $tableAlias = $attribute->getAttributeCode() . '_idx';
        return [$connection->quoteInto($tableAlias . '.value IN (?)', $value)];
    }

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param FilterInterface $filter
     * @return array
     */
    public function getCount($filter)
    {
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        $attribute = $filter->getAttributeModel();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());

        $fromAndJoins = $this->prepereCountFromAndJoins($select, $filter, $tableAlias);
        $whereConditions = $this->prepereCountWhereConditions($select, $tableAlias);

        $select
            ->reset()
            ->setPart(Select::FROM, $fromAndJoins)
            ->columns(
                [
                    'value' => $tableAlias . '.value',
                    'entity_id' => $tableAlias . '.entity_id'
                ]
            )
            ->group([$tableAlias . '.value', $tableAlias . '.entity_id']);

        if ($whereConditions) {
            $where[] = implode(' AND ', $whereConditions);
            $select->setPart(Select::WHERE, $where);
        }

        $mainSelect = clone $select;
        $mainSelect
            ->reset()
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $select))],
                []
            )
            ->columns(
                [
                    'value' => 'main_table.value',
                    'count' => new \Zend_Db_Expr('COUNT(main_table.entity_id)')
                ]
            )
            ->group('main_table.value');

        return $this->getConnection()->fetchAssoc($mainSelect);
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
                    'tableName'     => $this->getTable('catalog_product_index_eav'),
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
}
