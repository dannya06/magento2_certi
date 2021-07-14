<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\Data\ExtrafeeInvoiceInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeInvoice;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateFeeInvoiceTable
{
    /**
     * @param SchemaSetupInterface $installer
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(ExtrafeeInvoice::TABLE_NAME)
        )->addColumn(
            ExtrafeeInvoiceInterface::ENTITY_ID,
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            ExtrafeeInvoiceInterface::BASE_TOTAL,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Fee Amount'
        )->addColumn(
            ExtrafeeInvoiceInterface::TOTAL,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Fee Amount'
        )->addColumn(
            ExtrafeeInvoiceInterface::BASE_TAX,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Tax Amount'
        )->addColumn(
            ExtrafeeInvoiceInterface::TAX,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Tax Amount'
        )->addColumn(
            ExtrafeeInvoiceInterface::LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Fee Label'
        )->addColumn(
            ExtrafeeInvoiceInterface::OPTION_LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Fee Option Label'
        )->addColumn(
            ExtrafeeInvoiceInterface::ORDER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            ExtrafeeInvoiceInterface::INVOICE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Invoice Id'
        )->addColumn(
            ExtrafeeInvoiceInterface::FEE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Fee Id'
        )->addColumn(
            ExtrafeeInvoiceInterface::OPTION_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Option Id'
        )->addIndex(
            $installer->getIdxName(ExtrafeeInvoice::TABLE_NAME, [ExtrafeeInvoiceInterface::ORDER_ID]),
            [ExtrafeeInvoiceInterface::ORDER_ID]
        )->addIndex(
            $installer->getIdxName(ExtrafeeInvoice::TABLE_NAME, [ExtrafeeInvoiceInterface::INVOICE_ID]),
            [ExtrafeeInvoiceInterface::INVOICE_ID]
        )->addForeignKey(
            $installer->getFkName(
                ExtrafeeInvoice::TABLE_NAME,
                ExtrafeeInvoiceInterface::ORDER_ID,
                'sales_order',
                'entity_id'
            ),
            ExtrafeeInvoiceInterface::ORDER_ID,
            $installer->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                ExtrafeeInvoice::TABLE_NAME,
                ExtrafeeInvoiceInterface::INVOICE_ID,
                'sales_invoice',
                'entity_id'
            ),
            ExtrafeeInvoiceInterface::INVOICE_ID,
            $installer->getTable('sales_invoice'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Extrafee Invoice'
        );

        $installer->getConnection()->createTable($table);
    }
}
