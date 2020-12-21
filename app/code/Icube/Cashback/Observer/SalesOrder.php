<?php

namespace Icube\Cashback\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrder implements ObserverInterface
{
	protected $cashbackFactory;
	protected $ruleRepository;

	public function __construct(
        \Icube\Cashback\Model\CashbackFactory $cashbackFactory,
        \Magento\SalesRule\Model\RuleRepository $ruleRepository,
        \Icube\Cashback\Helper\Data $helper
    ) {
        $this->cashbackFactory= $cashbackFactory;  
        $this->ruleRepository = $ruleRepository;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getState() != 'complete') {
            return;
        }

        $appliedRuleIds = $order->getAppliedRuleIds();
        $subtotal = $order->getBaseSubtotal();
        $shippingAmount = $order->getBaseShippingAmount();

        $getCashback = $this->_helper->getCashback($appliedRuleIds, $subtotal, $shippingAmount);

        if ($getCashback['is_cashback'] === false) {
            return;
        }

        $cashbackList = $this->cashbackFactory->create()->getCollection()->addFieldToFilter('order_id',array('eq'=>$order->getId()));
        if(!empty($cashbackList->getData())){
            return;
        }

        $promo_names = [];
        foreach ($getCashback['data'] as $promo) {
            array_push($promo_names, $promo['promo_name']);
        }

        $cashback = $this->cashbackFactory->create();
        $cashback->setOrderId($order->getId());
        $cashback->setCashbackAmount($getCashback['total_cashback']);
        $cashback->setPromoName(implode(', ', $promo_names));
        $cashback->setStatus('NEW');
        $cashback->save();
    }
}
