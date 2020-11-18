<?php

namespace Icube\CancelOrderEmail\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Icube\CancelOrderEmail\Helper\Data as CancelOrderSender;

class OrderCancellationEmail implements ObserverInterface
{
    const XML_ENABLE = 'sales_email/order_cancel/enabled';

    protected $cancelSender;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CancelOrderSender $cancelSender
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->cancelSender = $cancelSender;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $enabled = $this->_scopeConfig->getValue(self::XML_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enabled == 1) {
            $order = $observer->getData('order');
            $this->cancelSender->send($order);
        }
    }
}