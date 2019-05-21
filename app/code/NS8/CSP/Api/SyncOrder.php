<?php
namespace NS8\CSP\Api;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Logger;
use NS8\CSP\Helper\Order;

class SyncOrder
{
    private $configHelper;
    private $logger;
    private $orderHelper;
    private $jsonResultFactory;
    private $response;

    public function __construct(
        Config $configHelper,
        Logger $logger,
        Order $orderHelper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\Webapi\Rest\Response $response
    ) {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->orderHelper = $orderHelper;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->response = $response;
    }

    /**
     * Set the order risk on the order and order grid
     *
     * @api
     * @param string $orderId
     * @throws \Exception
     * @return array
     */
    public function set($orderId)
    {
        try {
            $this->orderHelper->sync($orderId);

            //  have to wrap the response in an array per magento format
            return [
                [
                    'code' => 200,
                    'message' => 'OK'
                ]
            ];
        } catch (\Exception $e) {
            $this->logger->error("SyncOrder.set", $e->getMessage(), $orderId);
            throw $e;
        }
    }
}
