<?php

namespace Icube\Cashback\Controller\Ajax;

class CheckCashback extends \Magento\Framework\App\Action\Action
{
    protected $cart;
    protected $ruleRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Icube\Cashback\Helper\Data $helper
    ) {
        $this->_cart = $cart;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $quote = $this->_cart->getQuote();

        $appliedRuleIds = $quote->getAppliedRuleIds();
        $subtotal = $quote->getSubtotal();
        $shippingAmount = $quote->getShippingAddress()->getShippingAmount();

        echo json_encode($this->_helper->getCashback($appliedRuleIds, $subtotal, $shippingAmount));
    }
}
