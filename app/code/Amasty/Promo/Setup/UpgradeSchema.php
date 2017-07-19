<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */

namespace Amasty\Promo\Setup;

/**
 * Class UpgradeSchema
 *
 * @author Artem Brunevski
 */

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'top_banner_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Banner Image'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'top_banner_alt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Banner Alt'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'top_banner_on_hover_text',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Banner On Hover Text'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'top_banner_link',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Banner Link'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'top_banner_show_gift_images',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => false,
                    'comment' => 'Show Gift Images'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'top_banner_description',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => '64k',
                    'comment' => 'Banner Description'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'after_product_banner_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Banner Image'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'after_product_banner_alt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Banner Alt'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'after_product_banner_on_hover_text',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Banner On Hover Text'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'after_product_banner_link',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Banner Link'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'after_product_banner_show_gift_images',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => false,
                    'comment' => 'Show Gift Images'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'after_product_banner_description',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => '64k',
                    'comment' => 'Banner Description'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'label_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Label Image'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'label_image_alt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => 255,
                    'comment' => 'Label Image Alt'
                ]
            );

            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'top_banner_image',
                'top_banner_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => '128k',
                    'comment' => 'Banner Image'
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'after_product_banner_image',
                'after_product_banner_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => '128k',
                    'comment' => 'Banner Image'
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('amasty_ampromo_rule'),
                'label_image',
                'label_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => '128k',
                    'comment' => 'Label Image'
                ]
            );
        }
    }
}