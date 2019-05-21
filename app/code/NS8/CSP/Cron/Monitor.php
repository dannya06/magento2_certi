<?php
namespace NS8\CSP\Cron;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Logger;
use NS8\CSP\Helper\Order;

class Monitor
{
    private $logger;
    private $configHelper;
    private $orderHelper;
    private $resourceConnection;

    public function __construct(
        Config $configHelper,
        Logger $logger,
        Order $orderHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->logger = $logger;
        $this->orderHelper = $orderHelper;
        $this->configHelper = $configHelper;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute()
    {
        try {
            $this->orderHelper->processQueue();
        } catch (\Exception $e) {
            $this->logger->error("cron", "execute", $e->getMessage());
        }
    }
}
