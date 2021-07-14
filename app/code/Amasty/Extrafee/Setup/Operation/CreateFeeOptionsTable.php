<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\ResourceModel\Fee;
use Amasty\Extrafee\Model\ResourceModel\Option;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateFeeOptionsTable
{
    /**
     * @param SchemaSetupInterface $installer
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(Option::TABLE_NAME)
        )->addColumn(
            'entity_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'fee_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Fee Id'
        )->addColumn(
            'price',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Price'
        )->addColumn(
            'order',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Order'
        )->addColumn(
            'price_type',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Price Type'
        )->addColumn(
            'default',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => false],
            'Default'
        )->addColumn(
            'admin',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Admin Label'
        )->addColumn(
            'options_serialized',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Options Serialized'
        )->addForeignKey(
            $installer->getFkName(
                Option::TABLE_NAME,
                'fee_id',
                Fee::TABLE_NAME,
                FeeInterface::ENTITY_ID
            ),
            'fee_id',
            $installer->getTable(Fee::TABLE_NAME),
            FeeInterface::ENTITY_ID,
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Extrafee Option'
        );

        $installer->getConnection()->createTable($table);
    }
}
