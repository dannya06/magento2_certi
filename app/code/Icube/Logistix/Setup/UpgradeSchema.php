<?php

namespace Icube\Logistix\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Media;
use Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter;
use Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
             $table  = $setup->getConnection()
            ->newTable($setup->getTable('icube_logistix_queue'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'order_id', 
                Table::TYPE_INTEGER, 
                null, 
                [
                    'nullable' => false, 
                    'comment'=>'Transaction Id'
                ])
            ->addColumn(
                'shipment_id', 
                Table::TYPE_TEXT, 
                30, 
                [
                    'nullable' => false, 
                    'comment'=>'Shipment number from Magento'
                ])
            ->addColumn(
                'no_resi', 
                Table::TYPE_TEXT, 
                null, 
                [
                    'nullable' => true, 
                    'default' => null,
                    'comment' => 'Nomor Resi'
                ])
            ->addColumn(
                'shipment_date', 
                Table::TYPE_DATETIME, 
                null, 
                [
                
                    'nullable' => true,
                    'comment' => 'Shipment Date'
                ])
            ->addColumn(
                'comment', 
                Table::TYPE_TEXT, 
                null, 
                [
                
                    'nullable' => true,
                    'comment' => 'Comment'
                ])
            ->addColumn(
                'status', 
                Table::TYPE_INTEGER, 
                null, 
                [
                    'nullable' => false,
                    'default' => 0,
                    'comment'=>'Status from API. 1:create order done,2:get tracking done,3:activate done:'
                ])
            ->addColumn(
                'result_id', 
                Table::TYPE_TEXT, 
                null, 
                [
                
                    'nullable' => true,
                    'comment' => 'Result Id from prev API process'
                ]);
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}