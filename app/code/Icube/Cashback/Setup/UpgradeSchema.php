<?php

namespace Icube\Cashback\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {

                $tableName = 'icube_cashback';

                $setup->getConnection()->addColumn(
                    $setup->getTable($tableName),
                    'promo_name',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => 255,
                        'after' => 'order_id',
                        'comment' => 'name of the promo'
                    ]
                );
                $setup->getConnection()->addColumn(
                    $setup->getTable($tableName),
                    'description',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => 255,
                        'after' => 'status',
                        'comment' => 'description'
                    ]
                );
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            // table name
            $tableName = 'salesrule';
            // extra column(s)
            $columns = [
                'max_cashback' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Max. Cashback'
                ]
            ];
            // looping each column
            foreach ($columns as $columnName => $columnValue) {
                if ($setup->getConnection()->tableColumnExists($tableName, $columnName) === false) {
                    $setup->getConnection()->addColumn($tableName, $columnName, $columnValue);
                }
            }
        }

        $setup->endSetup();
    }
}