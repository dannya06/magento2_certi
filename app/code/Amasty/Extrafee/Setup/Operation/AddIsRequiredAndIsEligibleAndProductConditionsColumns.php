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

class AddIsRequiredAndIsEligibleAndProductConditionsColumns
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(Fee::TABLE_NAME),
            FeeInterface::IS_REQUIRED,
            [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is Required Fee'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable(Fee::TABLE_NAME),
            FeeInterface::IS_ELIGIBLE_REFUND,
            [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is Eligible For Refund Fee'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable(Fee::TABLE_NAME),
            FeeInterface::IS_PER_PRODUCT,
            [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => false,
                'comment' => 'Is Apply fee per product'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable(Fee::TABLE_NAME),
            FeeInterface::PRODUCT_CONDITIONS_SERIALIZED,
            [
                'type' => Table::TYPE_TEXT,
                'size' => '64k',
                'nullable' => true,
                'comment' => 'Product Conditions'
            ]
        );
    }
}
