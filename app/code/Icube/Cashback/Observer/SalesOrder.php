<?php

namespace Icube\Cashback\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Icube\Cashback\Helper\Data as cashbackHelperData;

class SalesOrder implements ObserverInterface
{
	protected $cashbackFactory;
	protected $ruleRepository;

	public function __construct(
        \Icube\Cashback\Model\CashbackFactory $cashbackFactory,
        \Magento\SalesRule\Model\RuleRepository $ruleRepository
    ){
        $this->cashbackFactory= $cashbackFactory;  
        $this->ruleRepository = $ruleRepository;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
           if($order->getState() == 'complete') {
                if($order->getAppliedRuleIds() != NULL){
                	$rule = $this->ruleRepository->getById($order->getAppliedRuleIds());

                    $cashback = $this->cashbackFactory->create();
                    $cashback->setOrderId($order->getId());

                    if($rule->getSimpleAction() == cashbackHelperData::CASHBACK_FIXED){
                        $cashback->setCashbackAmount($rule->getDiscountAmount());
                    }
                    if($rule->getSimpleAction() == cashbackHelperData::CASHBACK_PERCENT){
                        $total = 0;
                        if($rule->getApplyToShipping() == 1){
                            $total = $order->getBaseSubtotal() + $order->getBaseShippingAmount();
                        }else{
                            $total = $order->getBaseSubtotal();
                        }

                        $cashbackPercentAmount = $total * $rule->getDiscountAmount() / 100;
                        $cashback->setCashbackAmount($cashbackPercentAmount);
                    }
                        $cashback->setPromoName($rule->getName());
                		$cashback->setStatus('NEW');
                		$cashback->save();
                }
           }
    }
}
