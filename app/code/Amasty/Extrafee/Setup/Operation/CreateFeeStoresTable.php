<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Model\ResourceModel\Fee;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateFeeStoresTable
{
    /**
     * @param SchemaSetupInterface $installer
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(Fee::STORE_TABLE_NAME)
        )->addColumn(
            'fee_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Fee ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName(Fee::STORE_TABLE_NAME, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName(
                Fee::STORE_TABLE_NAME,
                'fee_id',
                Fee::TABLE_NAME,
                'entity_id'
            ),
            'fee_id',
            $installer->getTable(Fee::TABLE_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                Fee::STORE_TABLE_NAME,
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Extrafee To Store Linkage Table'
        );

        $installer->getConnection()->createTable($table);
    }
}
