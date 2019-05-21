<?php
namespace NS8\CSP\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Logger;

class Uninstall implements UninstallInterface
{
    private $logger;
    private $configHelper;
    private $restClient;

    public function __construct(
        Config $configHelper,
        Logger $logger,
        \NS8\CSP\Helper\RESTClient $restClient
    ) {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->restClient = $restClient;
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->configHelper->setAdminAreaCode();

        $this->logger->info('hostUninstall', 'Uninstalling CSP app...');

        $params = [
            "shop" => $this->configHelper->getStore(),
            "accessToken" => $this->configHelper->getAccessToken()
        ];

        $this->restClient->post("protect/magento/uninstall", $params);
        $setup->endSetup();
    }
}
