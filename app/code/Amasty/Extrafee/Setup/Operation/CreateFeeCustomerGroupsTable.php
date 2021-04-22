<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\ResourceModel\Fee;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateFeeCustomerGroupsTable
{
    /**
     * @param SchemaSetupInterface $installer
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $describe = $installer->getConnection()->describeTable($installer->getTable('customer_group'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable(Fee::GROUP_TABLE_NAME)
        )->addColumn(
            'fee_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Fee Id'
        )->addColumn(
            'customer_group_id',
            $describe['customer_group_id']['DATA_TYPE'] == 'int' ? Table::TYPE_INTEGER : Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group Id'
        )->addIndex(
            $installer->getIdxName(Fee::GROUP_TABLE_NAME, ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName(
                Fee::GROUP_TABLE_NAME,
                'fee_id',
                Fee::TABLE_NAME,
                FeeInterface::ENTITY_ID
            ),
            'fee_id',
            $installer->getTable(Fee::TABLE_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                Fee::GROUP_TABLE_NAME,
                'customer_group_id',
                $installer->getTable('customer_group'),
                'customer_group_id'
            ),
            'customer_group_id',
            $installer->getTable('customer_group'),
            'customer_group_id',
            Table::ACTION_CASCADE
        )->setComment('Amasty Extrafee To Customer Groups Relations');

        $installer->getConnection()->createTable($table);
    }
}
