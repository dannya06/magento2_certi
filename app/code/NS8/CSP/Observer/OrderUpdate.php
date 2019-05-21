<?php
namespace NS8\CSP\Observer;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Logger;
use NS8\CSP\Helper\Order;

class OrderUpdate implements \Magento\Framework\Event\ObserverInterface
{
    private $logger;
    private $configHelper;
    private $orderHelper;

    public function __construct(
        Config $configHelper,
        Logger $logger,
        Order $orderHelper
    ) {
        $this->logger = $logger;
        $this->orderHelper = $orderHelper;
        $this->configHelper = $configHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $this->orderHelper->update($order);
            return $this;
        } catch (\Exception $e) {
            $this->logger->error("order update", $e->getMessage());
            return false;
        }
    }
}
