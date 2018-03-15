<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\Layerednav\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->addFilterTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * Add filter table
     *
     * @param SchemaSetupInterface $setup
     */
    private function addFilterTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'aw_layerednav_filter'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'default_title',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Default Title'
            )
            ->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Code'
            )
            ->addColumn(
                'type',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Type'
            )
            ->addColumn(
                'is_filterable',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Filterable'
            )
            ->addColumn(
                'is_filterable_in_search',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Filterable In Search'
            )
            ->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
            )
            ->addColumn(
                'category_mode',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'Display On Category Mode'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['code']),
                ['code']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['type']),
                ['type']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['is_filterable']),
                ['is_filterable']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['is_filterable_in_search']),
                ['is_filterable_in_search']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['position']),
                ['position']
            )
            ->setComment('AW Layered Navigation Filter Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_title'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_title'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Value'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_title', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_title', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_title', ['value']),
                ['value']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_title', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_title', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Title Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_display_state'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_display_state'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false,],
                'Value'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_display_state', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_display_state', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_display_state', ['value']),
                ['value']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_display_state', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_display_state', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Display State Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_sort_order'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_sort_order'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Value'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_sort_order', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_sort_order', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_sort_order', ['value']),
                ['value']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_sort_order', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_sort_order', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Sort Order Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_exclude_category'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_exclude_category'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Category Id'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_exclude_category', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_exclude_category', ['category_id']),
                ['category_id']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_exclude_category', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'aw_layerednav_filter_exclude_category',
                    'category_id',
                    'catalog_category_entity',
                    'entity_id'
                ),
                'category_id',
                $setup->getTable('catalog_category_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Exclude Category Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_category'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_category'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'param_name',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => true, 'nullable' => false,],
                'Param Name'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Value'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_category', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_category', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_category', ['param_name']),
                ['value']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_category', ['value']),
                ['value']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_category', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_category', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Category Table');
        $setup->getConnection()->createTable($table);
    }
}
