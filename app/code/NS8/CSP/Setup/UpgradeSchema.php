<?php
namespace NS8\CSP\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Logger;
use NS8\CSP\Helper\Setup;

class UpgradeSchema implements UpgradeSchemaInterface
{
    private $logger;
    private $setupHelper;
    private $configHelper;

    public function __construct(
        Config $configHelper,
        Logger $logger,
        Setup $setupHelper
    ) {
        $this->logger = $logger;
        $this->setupHelper = $setupHelper;
        $this->configHelper = $configHelper;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->logger->info('hostUpgrade', 'Upgrading CSP app...');
        $setup->startSetup();

        $this->setupHelper->setupSchema($setup);
        $this->setupHelper->setupAccount($setup);

        $setup->endSetup();
    }
}
