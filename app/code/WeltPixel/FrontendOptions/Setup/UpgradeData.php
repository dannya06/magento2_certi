<?php
namespace WeltPixel\FrontendOptions\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $configDataTable = $setup->getTable('core_config_data');

        if ( version_compare( $context->getVersion(), '1.1.1' ) < 0 ) {
            $connection->delete($configDataTable, '`path` LIKE "%weltpixel_frontend_options/paragraph%"');
        }

        $setup->endSetup();
    }
}
