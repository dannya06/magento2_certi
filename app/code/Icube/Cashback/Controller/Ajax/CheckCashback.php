<?php
namespace Icube\Cashback\Controller\Ajax;
use Magento\Framework\App\Action\Context;
use Icube\Cashback\Helper\Data as cashbackHelperData;

class CheckCashback extends \Magento\Framework\App\Action\Action
{
    protected $cart;
    protected $ruleRepository;

    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\RuleRepository $ruleRepository,
        \Icube\Cashback\Helper\Data $cashbackHelperData
    )
    {
        $this->cart = $cart;
        $this->ruleRepository = $ruleRepository;
        parent::__construct($context);
    }
    public function execute()
    {
       $quote = $this->cart->getQuote();
       $shippingAmount = $quote->getShippingAddress()->getShippingAmount();
       $subTotal = $quote->getSubtotal();


       $return = [
            'is_cashback' => false
       ];

       if($quote->getAppliedRuleIds()!== null){
            $rule = $this->ruleRepository->getById($quote->getAppliedRuleIds());
            if($rule->getSimpleAction() == cashbackHelperData::CASHBACK_FIXED){
                $data = [
                    'promo_name' => $rule->getName(),
                    'amount' => $rule->getDiscountAmount()
                ];
                $return['is_cashback'] = true;
                $return['data'] = $data;
            }
            if($rule->getSimpleAction() == cashbackHelperData::CASHBACK_PERCENT){
                $total = $subTotal;
                if($rule->getApplyToShipping() == 1){
                    $total += $shippingAmount;
                }
                $cashbackPercentAmount = $total * $rule->getDiscountAmount() / 100;
                $data = [
                    'promo_name' => $rule->getName(),
                    'amount' => $cashbackPercentAmount
                ];
                $return['is_cashback'] = true;
                $return['data'] = $data;
            }
       }

       echo json_encode($return);
    }
}
?>