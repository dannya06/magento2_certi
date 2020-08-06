<?php

namespace Icube\CancelOrderEmail\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;

class OrderCancellationEmail implements ObserverInterface
{
    const XML_ENABLE = 'sales_email/order_cancel/enabled';
    const XML_TEMPLATE = 'sales_email/order_cancel/template';
    const XML_IDENTITY = 'sales_email/order_cancel/identity';
    const XML_COPY_TO = 'sales_email/order_cancel/copy_to';
    const XML_COPY_METHOD = 'sales_email/order_cancel/copy_method';

    protected $_transportBuilder;
    protected $_senderResolver;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var OrderResource
     */
    protected $orderResource;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * @var IdentityInterface
     */
    protected $identityContainer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        SenderResolverInterface $senderResolver,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        OrderResource $orderResource,
        OrderIdentity $identityContainer
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_senderResolver = $senderResolver;
        $this->paymentHelper = $paymentHelper;
        $this->orderResource = $orderResource;
        $this->addressRenderer = $addressRenderer;
        $this->identityContainer = $identityContainer;
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
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->identityContainer->getStore()->getStoreId(),
            ])
            ->setTemplateVars([
                'order' => $order,
                'billing' => $order->getBillingAddress(),
                'store' => $order->getStore(),
                'payment_html' => $this->paymentHelper->getInfoBlockHtml($order->getPayment(), $this->identityContainer->getStore()->getStoreId()),
                'formattedShippingAddress' => $order->getIsVirtual() ? null : $this->addressRenderer->format($order->getShippingAddress(), 'html'),
                'formattedBillingAddress' => $this->addressRenderer->format($order->getBillingAddress(), 'html'),
                'order_data' => [
                    'customer_name' => $order->getCustomerName(),
                    'is_not_virtual' => $order->getIsNotVirtual(),
                    'email_customer_note' => $order->getEmailCustomerNote(),
                    'frontend_status_label' => $order->getFrontendStatusLabel()
                ]
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