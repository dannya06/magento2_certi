<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */

/**
 * Class UpgradeSchema
 *
 * @author Artem Brunevski
 */

namespace Amasty\Extrafee\Setup;

use Amasty\Extrafee\Model\Config\Source\Excludeinclude;
use Magento\Framework\DB\Ddl\Table as DdlTable;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addCalculationColumns($setup);
        }

        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $this->changeIdColumnType($setup);
        }

        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            $this->addTaxColumns($setup);
            $this->addBankFeeColumns($setup);
            
        }

        $setup->endSetup();
    }

    protected function addCalculationColumns(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('amasty_extrafee');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'discount_in_subtotal',
            [
                'type' => DdlTable::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Excludeinclude::VAR_DEFAULT,
                'comment' => 'Discount In Subtotal'
            ]
        );

        $connection->addColumn(
            $table,
            'tax_in_subtotal',
            [
                'type' => DdlTable::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Excludeinclude::VAR_DEFAULT,
                'comment' => 'Tax In Subtotal'
            ]
        );

        $connection->addColumn(
            $table,
            'shipping_in_subtotal',
            [
                'type' => DdlTable::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Excludeinclude::VAR_DEFAULT,
                'comment' => 'Shipping In Subtotal'
            ]
        );
    }

    protected function changeIdColumnType(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->changeColumn(
                $setup->getTable('amasty_extrafee_quote'),
                'entity_id',
                'entity_id',
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

    protected function addTaxColumns(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('amasty_extrafee_quote');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'tax_amount',
            [
                'type' => DdlTable::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'Tax'
            ]
        );

        $connection->addColumn(
            $table,
            'base_tax_amount',
            [
                'type' => DdlTable::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'Tax'
            ]
        );
    }

    protected function addBankFeeColumns(SchemaSetupInterface $setup)
    {
        /*$connection = $setup->getConnection();
        $sql = "INSERT INTO `amasty_extrafee` (`entity_id`, `enabled`, `name`, `sort_order`, `frontend_type`, `description`, `options_serialized`, `conditions_serialized`, `discount_in_subtotal`, `tax_in_subtotal`, `shipping_in_subtotal`) VALUES (1, 1, 'Unique Code Bank Transfer', 0, 'radio', NULL, NULL, '', 2, 2, 2);";

        $connection->query($sql);

        $sql = "INSERT INTO `amasty_extrafee_customer_group` (`fee_id`, `customer_group_id`) VALUES (1, 0),(1, 1),(1, 2),(1, 3);";

        $connection->query($sql);

        $sql = "INSERT INTO `amasty_extrafee_option` (`entity_id`, `fee_id`, `price`, `order`, `price_type`, `default`, `admin`, `options_serialized`) VALUES (1, 1, 333.0000, 1, 'fixed', 1, 'Unique code', '[\"Unique code\",\"\"]');";

        $connection->query($sql);

        $sql = "INSERT INTO `amasty_extrafee_store` (`fee_id`, `store_id`) VALUES (1, 0);";

        $connection->query($sql);*/
    }
}
