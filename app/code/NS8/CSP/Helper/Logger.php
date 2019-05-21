<?php
namespace NS8\CSP\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Logger extends AbstractHelper
{
    private $logger;
    private $restClient;
    private $configHelper;

    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \NS8\CSP\Helper\Config $configHelper,
        \NS8\CSP\Helper\RESTClient $restClient
    ) {
        $this->logger = $loggerInterface;
        $this->configHelper = $configHelper;
        $this->restClient = $restClient;
    }

    public function error($function, $log, $data = null)
    {
        $this->logger->error($log);
        $this->log($function, $log, $data, 1);
    }

    public function warn($function, $log, $data = null)
    {
        $this->logger->error($log);
        $this->log($function, $log, $data, 2);
    }

    public function info($function, $log, $data = null)
    {
        $this->logger->info($log);
        $this->log($function, $log, $data, 3);
    }

    private function log($function, $log, $data = null, $level = 1)
    {
        try {
            //  log to the cloud
            $data = [
                'level' => $level,
                'category' => 'magento ns8csp',
                'data' => [
                    'platform' => 'magento',
                    'projectId' => $this->configHelper->getProjectId(),
                    'shop' => $this->configHelper->getStore(),
                    'function' => $function,
                    'message' => $log,
                    'data' => $data,
                    "magentoVersion" => $this->configHelper->getMagentoVersion(),
                    "moduleVersion" => $this->configHelper->getExtensionVersion(),
                    "phpVersion" => PHP_VERSION,
                    "phpOS" => PHP_OS
                ]
            ];

            $this->restClient->post("ops/logs", $data, null, 2);
        } catch (\Exception $e) {
            $this->logger->error('ns8csp.log: '.$e->getMessage());
        } finally {
            return true;
        }
    }
}
