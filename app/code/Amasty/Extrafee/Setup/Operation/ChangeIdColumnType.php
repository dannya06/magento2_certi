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

class ChangeIdColumnType
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->changeColumn(
                $setup->getTable(ExtrafeeQuote::TABLE_NAME),
                ExtrafeeQuoteInterface::ENTITY_ID,
                ExtrafeeQuoteInterface::ENTITY_ID,
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 11,
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'comment' => 'Entity ID'
                ]
            );
    }
}
