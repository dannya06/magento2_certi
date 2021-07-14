<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\Data\ExtrafeeCreditmemoInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeCreditmemo;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateFeeCreditMemoTable
{
    /**
     * @param SchemaSetupInterface $installer
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(ExtrafeeCreditmemo::TABLE_NAME)
        )->addColumn(
            ExtrafeeCreditmemoInterface::ENTITY_ID,
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            ExtrafeeCreditmemoInterface::BASE_TOTAL,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Fee Amount'
        )->addColumn(
            ExtrafeeCreditmemoInterface::TOTAL,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Fee Amount'
        )->addColumn(
            ExtrafeeCreditmemoInterface::BASE_TAX,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Tax Amount'
        )->addColumn(
            ExtrafeeCreditmemoInterface::TAX,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Tax Amount'
        )->addColumn(
            ExtrafeeCreditmemoInterface::LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Fee Label'
        )->addColumn(
            ExtrafeeCreditmemoInterface::OPTION_LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Fee Option Label'
        )->addColumn(
            ExtrafeeCreditmemoInterface::ORDER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            ExtrafeeCreditmemoInterface::CREDITMEMO_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Credit Memo Id'
        )->addColumn(
            ExtrafeeCreditmemoInterface::FEE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Fee Id'
        )->addColumn(
            ExtrafeeCreditmemoInterface::OPTION_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Option Id'
        )->addIndex(
            $installer->getIdxName(ExtrafeeCreditmemo::TABLE_NAME, [ExtrafeeCreditmemoInterface::ORDER_ID]),
            [ExtrafeeCreditmemoInterface::ORDER_ID]
        )->addIndex(
            $installer->getIdxName(ExtrafeeCreditmemo::TABLE_NAME, [ExtrafeeCreditmemoInterface::CREDITMEMO_ID]),
            [ExtrafeeCreditmemoInterface::CREDITMEMO_ID]
        )->addForeignKey(
            $installer->getFkName(
                ExtrafeeCreditmemo::TABLE_NAME,
                ExtrafeeCreditmemoInterface::ORDER_ID,
                'sales_order',
                'entity_id'
            ),
            ExtrafeeCreditmemoInterface::ORDER_ID,
            $installer->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                ExtrafeeCreditmemo::TABLE_NAME,
                ExtrafeeCreditmemoInterface::CREDITMEMO_ID,
                'sales_creditmemo',
                'entity_id'
            ),
            ExtrafeeCreditmemoInterface::CREDITMEMO_ID,
            $installer->getTable('sales_creditmemo'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Extrafee CreditMemo'
        );

        $installer->getConnection()->createTable($table);
    }
}
