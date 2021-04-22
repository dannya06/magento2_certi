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

class CreateExtrafeeTable
{
    /**
     * @param SchemaSetupInterface $installer
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(Fee::TABLE_NAME)
        )->addColumn(
            FeeInterface::ENTITY_ID,
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            FeeInterface::ENABLED,
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => false],
            'Enabled'
        )->addColumn(
            FeeInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Name'
        )->addColumn(
            FeeInterface::SORT_ORDER,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true, 'default' => '0'],
            'Sort Order ID'
        )->addColumn(
            FeeInterface::FRONTEND_TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Frontend Type'
        )->addColumn(
            FeeInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Description'
        )->addColumn(
            'options_serialized',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Options Serialized'
        )->addColumn(
            FeeInterface::CONDITIONS_SERIALIZED,
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Conditions Serialized'
        )->setComment(
            'Amasty Extrafee'
        );

        $installer->getConnection()->createTable($table);
    }
}
