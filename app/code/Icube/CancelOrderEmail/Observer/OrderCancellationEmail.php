<?php

namespace Icube\CancelOrderEmail\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\SenderResolverInterface;

class OrderCancellationEmail implements ObserverInterface
{
    const XML_ENABLE = 'sales_email/order_cancel/enabled';
    const XML_TEMPLATE = 'sales_email/order_cancel/template';
    const XML_IDENTITY = 'sales_email/order_cancel/identity';
    const XML_COPY_TO = 'sales_email/order_cancel/copy_to';
    const XML_COPY_METHOD = 'sales_email/order_cancel/copy_method';

    protected $_transportBuilder;
    protected $_senderResolver;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        SenderResolverInterface $senderResolver
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_senderResolver = $senderResolver;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $enabled = $this->_scopeConfig->getValue(self::XML_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $copyTo = $this->_scopeConfig->getValue(self::XML_COPY_TO, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $copyMethod = $this->_scopeConfig->getValue(self::XML_COPY_METHOD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($enabled == 1) {
            $order = $observer->getData('order');
            try {
                $this->send($order, $copyTo, $copyMethod);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
            if ($copyMethod == 'copy') {
                try {
                    $this->sendCopyTo($order, $copyTo);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }

    public function configureEmailTemplate($order)
    {
        $template = $this->_scopeConfig->getValue(self::XML_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sender = $this->_scopeConfig->getValue(self::XML_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sendFrom = $this->_senderResolver->resolve($sender);

        $this->_transportBuilder
            ->setTemplateIdentifier($template)
            ->setTemplateOptions([
                'area' => 'frontend',
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars([
                'order' => $order,
                'template_subject' => $order->getStoreName().' Order Cancel',
                'customername' => $order->getCustomerName(),
                'email_content' => 'Your order #'.$order->getIncrementId().' has been canceled.',
            ])
            ->setFrom($sendFrom);
    }

    public function send($order, $copyTo, $copyMethod)
    {
        $this->configureEmailTemplate($order);
        $this->_transportBuilder->addTo(array($order->getCustomerEmail()));
        if (!empty($copyTo) && $copyMethod == 'bcc') {
            $arrCopy = explode(',', $copyTo);
            foreach ($arrCopy as $email) {
                $this->_transportBuilder->addBcc($email);
            }
        }
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
    }

    public function sendCopyTo($order, $copyTo)
    {
        if (!empty($copyTo)) {
            $arrCopy = explode(',', $copyTo);
            foreach ($arrCopy as $email) {
                $this->configureEmailTemplate($order);
                $this->_transportBuilder->addTo($email);
                $transport = $this->_transportBuilder->getTransport();
                $transport->sendMessage();
            }
        }
    }
}