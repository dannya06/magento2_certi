<?php

namespace Icube\BankList\Block\Checkout\Onepage\Success;

class PaymentInformation extends \Magento\Framework\View\Element\Template
{
    const BANKLIST = 'icube_banklist/general/banklist';

    protected $_storeScope;
    protected $_serialize;
    protected $_storeManager;
    protected $_checkoutSession;
    protected $_orderFactory;
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ){
        $this->_storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->_serialize = $serialize;
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    public function getStoreid()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }

    public function getBankList()
    {
        $banklistconfig = $this->_scopeConfig->getValue(self::BANKLIST, $this->_storeScope, $this->getStoreid());
        if($banklistconfig == '' || $banklistconfig == null)
            return;

        $unserializedata = $this->_serialize->unserialize($banklistconfig);
        $banklistarray = array();
        foreach($unserializedata as $key => $row) {
            if ($row['enable'] == 1) {
                $banklistarray[] = $row;
            }
        }
        return $banklistarray;
    }

    public function isBankTransfer()
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        $paymentCode = $order->getPayment()->getMethodInstance()->getCode();
        if($paymentCode == 'banktransfer') {
            return true;
        } else {
            return false;
        }
    }
}