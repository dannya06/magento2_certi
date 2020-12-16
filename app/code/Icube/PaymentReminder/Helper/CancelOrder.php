<?php

namespace Icube\PaymentReminder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class CancelOrder extends AbstractHelper 
{
	protected $orderManagement;
	protected $transportBuilder;
	protected $resultFactory;
    protected $orderFactory;
	protected $orderCollectionFactory;
	protected $date;
	protected $objectManager;
    protected $paymentHelper;
    protected $orderEmailFactory;
    protected $emailCreatedFactory;
    protected $identityContainer;
    protected $addressRenderer;

	public function __construct(
        \Magento\Framework\App\Helper\Context $context, 
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Framework\Controller\ResultFactory $resultFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Payment\Helper\Data $paymentHelper,
		\Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\Order $orderFactory,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Order\Email\Container\OrderIdentity $identityContainer,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Icube\PaymentReminder\Model\EmailnotificationFactory $emailCreatedFactory,
        \Icube\PaymentReminder\Model\Resource\Emailnotification\CollectionFactory $orderEmailFactory
	) {
	    $this->date = $date;
	    $this->transportBuilder = $transportBuilder;
	    $this->resultFactory = $resultFactory;
	    $this->objectManager = $objectManager;
        $this->paymentHelper = $paymentHelper;
        $this->resource = $resource;
	    $this->orderManagement = $orderManagement;
        $this->orderFactory = $orderFactory;
	    $this->orderCollectionFactory = $orderCollectionFactory;
        $this->identityContainer = $identityContainer;
        $this->addressRenderer = $addressRenderer;
        $this->emailCreatedFactory = $emailCreatedFactory;
        $this->orderEmailFactory = $orderEmailFactory;
	    parent::__construct($context);
	}

	public function checkPendingOrder()
    { 
        $active = $this->scopeConfig->getValue('icube_paymentreminder/general/active', ScopeInterface::SCOPE_STORE);
        $limit = $this->scopeConfig->getValue('icube_paymentreminder/general/expired_after', ScopeInterface::SCOPE_STORE);
        $notify_after = $this->scopeConfig->getValue('icube_paymentreminder/general/notify_after', ScopeInterface::SCOPE_STORE);

        if ($active) {
            $collection = $this->emailCreatedFactory->create()->getCollection()
            ->addFieldToFilter('status', array('in' => array('pending','pending_payment')))
            ->setOrder('entity_id','ASC')
            ->setPageSize(25);

            $datenow = StrToTime($this->date->gmtDate());
	        foreach ($collection as $order) {
                $orderId = $order->getEntityId();
                $realorder = $this->orderFactory->load($orderId);
                if (($realorder->getStatus() != 'pending') && ($realorder->getStatus() != 'pending_payment')) {
                    $order->setStatus($realorder->getStatus())->save(); 
                    continue;
                }
	         	$dateOrder = StrToTime($order->getCreatedAt());
	            $diff  = $datenow - $dateOrder;
				$hours = $diff / ( 60 * 60 );
                $statusNotification = $order->getIsNotice();
                if ($hours > $limit && $statusNotification == '1') {
                    $this->orderManagement->cancel($orderId);
                    $order->setStatus("cancel")->save(); 
                    $this->sendEmail($orderId, 'cancel');
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/emailnotif_cancel.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info($orderId);
                }
                if ($hours > ($limit - $notify_after) && $statusNotification != '1') {
                    $order->setIsNotice(1)->save();
                    $this->sendEmail($orderId, 'reminder');
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/emailnotif_reminder.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info($orderId);
                }
	        }
        }
	}

	public function sendEmail($orderId, $type)
    {
        $order = $this->orderFactory->load($orderId);
        $this->configureEmailTemplate($order, $type);
        $this->transportBuilder->addTo(array($order->getCustomerEmail()));
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        return true;
	}

    public function configureEmailTemplate($order, $type)
    {
        $template_cancel = $this->scopeConfig->getValue('icube_paymentreminder/general/template_cancel', ScopeInterface::SCOPE_STORE);
        $template_reminder = $this->scopeConfig->getValue('icube_paymentreminder/general/template_reminder', ScopeInterface::SCOPE_STORE);
        $email_sender = $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE);
        $email_name = $this->scopeConfig->getValue('trans_email/ident_support/name', ScopeInterface::SCOPE_STORE);
        $sender = [
            'name' => $email_name,
            'email' => $email_sender,
        ];

        if ($type == 'cancel') {
            $template = $template_cancel;
        } else {
            $template = $template_reminder;
        }

        $payment = $order->getPayment();

        $this->transportBuilder
            ->setTemplateIdentifier($template)
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->identityContainer->getStore()->getStoreId(),
            ])
            ->setTemplateVars([
                'order' => $order,
                'billing' => $order->getBillingAddress(),
                'store' => $order->getStore(),
                'paymentAdditionalInfo' => $payment->getAdditionalInformation(),
                'payment_html' => $this->paymentHelper->getInfoBlockHtml($payment, $this->identityContainer->getStore()->getStoreId()),
                'formattedShippingAddress' => $order->getIsVirtual() ? null : $this->addressRenderer->format($order->getShippingAddress(), 'html'),
                'formattedBillingAddress' => $this->addressRenderer->format($order->getBillingAddress(), 'html'),
                'order_data' => [
                    'customer_name' => $order->getCustomerName(),
                    'is_not_virtual' => $order->getIsNotVirtual(),
                    'email_customer_note' => $order->getEmailCustomerNote(),
                    'frontend_status_label' => $order->getFrontendStatusLabel()
                ]
            ])
            ->setFrom($sender);
    }

    public function migrateData()
    {
        $collection = $this->orderCollectionFactory->create()
                      ->addAttributeToFilter('status', array('in' => array('pending','pending_payment')));
        foreach ($collection as $order) {
            $data = array('entity_id' => $order->getEntityId(), 'is_notice' => NULL , 'status' => $order->getStatus(), 'created_at' => $order->getCreatedAt());
            $connection = $this->resource->getConnection();
            $themeTable = $this->resource->getTableName('icube_email_notification');
            $emailexist = $this->emailCreatedFactory->create()->load($order->getEntityId());
            if ($emailexist->getEntityId() == NULL){
                try {
                    $sql = "INSERT INTO " . $themeTable . "(entity_id, status,created_at) VALUES (".$order->getEntityId().", '".$order->getStatus()."','".$order->getCreatedAt()."')";
                    $connection->query($sql);
                } catch (Exception $e) { 
                    var_dump($e);
                }
            }
        }
    }
}