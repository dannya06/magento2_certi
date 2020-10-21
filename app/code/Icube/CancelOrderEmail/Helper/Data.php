<?php 

namespace Icube\CancelOrderEmail\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_TEMPLATE = 'sales_email/order_cancel/template';
    const XML_TEMPLATE_GUEST = 'sales_email/order_cancel/guest_template';
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
        Context $context,
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
		parent::__construct($context);
    }

    public function send($order)
    {
        $copyTo = $this->_scopeConfig->getValue(self::XML_COPY_TO, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $copyMethod = $this->_scopeConfig->getValue(self::XML_COPY_METHOD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        try {
            $this->sendTo($order, $copyTo, $copyMethod);
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

    public function configureEmailTemplate($order)
    {
        $sender = $this->_scopeConfig->getValue(self::XML_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sendFrom = $this->_senderResolver->resolve($sender);

        if ($order->getCustomerIsGuest()) {
            $template = $this->_scopeConfig->getValue(self::XML_TEMPLATE_GUEST, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = $this->_scopeConfig->getValue(self::XML_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $customerName = $order->getCustomerName();
        }

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
                    'customer_name' => $customerName,
                    'is_not_virtual' => $order->getIsNotVirtual(),
                    'email_customer_note' => $order->getEmailCustomerNote(),
                    'frontend_status_label' => $order->getFrontendStatusLabel()
                ]
            ])
            ->setFrom($sendFrom);
    }

    public function sendTo($order, $copyTo, $copyMethod)
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