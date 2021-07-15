<?php 

namespace Icube\Logistix\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('icube_ship_map'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Destination Id'
            )
            ->addColumn('city', Table::TYPE_TEXT, '255', [])
            ->addColumn('kecamatan_code', Table::TYPE_TEXT, '255', [])
            ->addColumn('target_ship_method', Table::TYPE_TEXT, '255', [])
            ->addColumn('target_city', Table::TYPE_TEXT, '255', [])
            ->addColumn('rule', Table::TYPE_TEXT, '255', []);

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}