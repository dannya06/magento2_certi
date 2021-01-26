<?php

namespace Icube\CartRuleBanner\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0', '<')) {

            $installer->getConnection()->addColumn(
                $installer->getTable('salesrule'),
                'cart_rule_banner_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'lenght' => '11',
                    'nullable' => true,
                    'comment' => 'Cart Rule Banner ID'
                ]
            );
        }

        $installer->endSetup();
    }
}