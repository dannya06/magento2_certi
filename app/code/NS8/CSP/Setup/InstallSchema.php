<?php
namespace NS8\CSP\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Logger;
use NS8\CSP\Helper\Setup;

class InstallSchema implements InstallSchemaInterface
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

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->setupHelper->setupSchema($setup);
	    $this->logger->info('hostInstall', 'Installing CSP app...');
        $this->setupHelper->setupAccount($setup);
        $setup->endSetup();
    }
}
