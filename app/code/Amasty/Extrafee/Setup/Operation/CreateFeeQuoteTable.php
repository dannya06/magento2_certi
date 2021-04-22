<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateFeeQuoteTable
{
    /**
     * @param SchemaSetupInterface $installer
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(ExtrafeeQuote::TABLE_NAME)
        )->addColumn(
            ExtrafeeQuoteInterface::ENTITY_ID,
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            ExtrafeeQuoteInterface::QUOTE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'ExtrafeeQuote ID'
        )->addColumn(
            ExtrafeeQuoteInterface::FEE_ID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Fee Id'
        )->addColumn(
            ExtrafeeQuoteInterface::OPTION_ID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Option Id'
        )->addColumn(
            ExtrafeeQuoteInterface::FEE_AMOUNT,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Fee Amount'
        )->addColumn(
            ExtrafeeQuoteInterface::BASE_FEE_AMOUNT,
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Fee Amount'
        )->addColumn(
            ExtrafeeQuoteInterface::LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Label'
        )->addIndex(
            $installer->getIdxName(
                ExtrafeeQuote::TABLE_NAME,
                [ExtrafeeQuoteInterface::QUOTE_ID, ExtrafeeQuoteInterface::FEE_ID, ExtrafeeQuoteInterface::OPTION_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [ExtrafeeQuoteInterface::QUOTE_ID, ExtrafeeQuoteInterface::FEE_ID, ExtrafeeQuoteInterface::OPTION_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Amasty Extrafee ExtrafeeQuote'
        );

        $installer->getConnection()->createTable($table);
    }
}
