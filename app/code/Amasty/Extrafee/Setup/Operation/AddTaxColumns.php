<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddTaxColumns
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(ExtrafeeQuote::TABLE_NAME);
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            ExtrafeeQuoteInterface::TAX_AMOUNT,
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'Tax'
            ]
        );

        $connection->addColumn(
            $table,
            ExtrafeeQuoteInterface::BASE_TAX_AMOUNT,
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'Tax'
            ]
        );
    }
}
