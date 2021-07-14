<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateFeeOrderTable
{
    /**
     * @param SchemaSetupInterface $installer
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(ExtrafeeOrder::TABLE_NAME)
        )->addColumn(
            ExtrafeeOrderInterface::ENTITY_ID,
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            ExtrafeeOrderInterface::BASE_TOTAL,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Fee Amount'
        )->addColumn(
            ExtrafeeOrderInterface::BASE_TOTAL_INVOICED,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Fee Amount Invoiced'
        )->addColumn(
            ExtrafeeOrderInterface::BASE_TOTAL_REFUNDED,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Fee Amount Refunded'
        )->addColumn(
            ExtrafeeOrderInterface::TOTAL,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Fee Amount'
        )->addColumn(
            ExtrafeeOrderInterface::TOTAL_INVOICED,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Fee Amount Invoiced'
        )->addColumn(
            ExtrafeeOrderInterface::TOTAL_REFUNDED,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Fee Amount Refunded'
        )->addColumn(
            ExtrafeeOrderInterface::BASE_TAX,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Tax Amount'
        )->addColumn(
            ExtrafeeOrderInterface::BASE_TAX_INVOICED,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Tax Amount Invoiced'
        )->addColumn(
            ExtrafeeOrderInterface::BASE_TAX_REFUNDED,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Tax Amount Refunded'
        )->addColumn(
            ExtrafeeOrderInterface::TAX,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Tax Amount'
        )->addColumn(
            ExtrafeeOrderInterface::TAX_INVOICED,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Tax Amount Invoiced'
        )->addColumn(
            ExtrafeeOrderInterface::TAX_REFUNDED,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Tax Amount Refunded'
        )->addColumn(
            ExtrafeeOrderInterface::LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Fee Label'
        )->addColumn(
            ExtrafeeOrderInterface::OPTION_LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Fee Option Label'
        )->addColumn(
            ExtrafeeOrderInterface::IS_REFUNDED,
            Table::TYPE_BOOLEAN,
            1,
            ['nullable' => false, 'default' => 0],
            'Is Refunded'
        )->addColumn(
            ExtrafeeOrderInterface::ORDER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            ExtrafeeOrderInterface::FEE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Fee Id'
        )->addColumn(
            ExtrafeeOrderInterface::OPTION_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Option Id'
        )->addIndex(
            $installer->getIdxName(ExtrafeeOrder::TABLE_NAME, [ExtrafeeOrderInterface::ORDER_ID]),
            [ExtrafeeOrderInterface::ORDER_ID]
        )->addForeignKey(
            $installer->getFkName(
                ExtrafeeOrder::TABLE_NAME,
                ExtrafeeOrderInterface::ORDER_ID,
                'sales_order',
                'entity_id'
            ),
            ExtrafeeOrderInterface::ORDER_ID,
            $installer->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Extrafee Order'
        );

        $installer->getConnection()->createTable($table);
    }
}
