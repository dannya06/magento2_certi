<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\Config\Source\Excludeinclude;
use Amasty\Extrafee\Model\ResourceModel\Fee;
use Magento\Framework\DB\Ddl\Table as DdlTable;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddCalculationColumns
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(Fee::TABLE_NAME);
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            FeeInterface::DISCOUNT_IN_SUBTOTAL,
            [
                'type' => DdlTable::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Excludeinclude::VAR_DEFAULT,
                'comment' => 'Discount In Subtotal'
            ]
        );

        $connection->addColumn(
            $table,
            FeeInterface::SHIPPING_IN_SUBTOTAL,
            [
                'type' => DdlTable::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Excludeinclude::VAR_DEFAULT,
                'comment' => 'Shipping In Subtotal'
            ]
        );
    }
}
