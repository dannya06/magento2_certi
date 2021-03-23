<?php

namespace Icube\CartRuleBanner\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('icube_cart_rule_banner')
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'conditions_serialized',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'cms_block_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'CMS Block ID'
        )->setComment(
            'Icube Cart Rule Banner'
        );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}