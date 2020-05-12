<?php

namespace Icube\Cashback\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CASHBACK_FIXED = 'cashback_fixed';
    const CASHBACK_PERCENT = 'cashback_percent';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory
    ) {
        parent::__construct($context);
        $this->cart = $cart;
        $this->ruleFactory = $ruleFactory;
    }

    public function getStaticCashbackTypes()
    {
        $types = [
            self::CASHBACK_FIXED => __('Cashback Fixed'),
            self::CASHBACK_PERCENT => __('Cashback Percent')

        ];

        return $types;
    }

    public function getCashbackTypes()
    {
        $types = $this->getStaticCashbackTypes();
        $values = [];
        $result = [];
        foreach($types as $v => $label){
            $values[] = [
                'value' => $v,
                'label' => $label
            ];
        }

        $result[] = [
            'label' => __('Cashback'),
            'value' => $values
        ];

        return $result;
    }

    public function getFilePath($rule)
    {
        $rule = implode('_', array_map('ucfirst', explode('_', $rule)));
        $rule = str_replace('_', '', $rule);
        $rule = 'Icube\Cashback\Model\Rule\Action\Discount\\' . $rule;

        return $rule;
    }

    public function getCashback($appliedRuleIds, $subtotal, $shippingAmount)
    {
        $reponse = [
            'is_cashback' => false,
            'total_cashback' => 0,
            'data' => []
        ];

        if (!is_null($appliedRuleIds)) {
            foreach (explode(',', $appliedRuleIds) as $ruleId) {
                $rule = $this->ruleFactory->create()->load($ruleId);
                $maxDiscount = intval($rule->getMaxCashback());

                if ($rule->getSimpleAction() == self::CASHBACK_FIXED) {
                    $total_cashback = ($maxDiscount > 0 && $maxDiscount < intval($rule->getDiscountAmount()))
                        ? $maxDiscount
                        : intval($rule->getDiscountAmount());

                    $reponse['is_cashback'] = true;
                    $reponse['total_cashback'] += $total_cashback;
                    array_push($reponse['data'], [
                        'promo_name' => $rule->getName(),
                        'amount' => $total_cashback
                    ]);
                }
                elseif ($rule->getSimpleAction() == self::CASHBACK_PERCENT) {
                    $total = ($rule->getApplyToShipping() == 1)? ($subtotal + $shippingAmount): $subtotal;

                    $total_cashback = $total * intval($rule->getDiscountAmount()) / 100;
                    $total_cashback = ($maxDiscount > 0 && $maxDiscount < $total_cashback)
                        ? $maxDiscount
                        : $total_cashback;

                    $reponse['is_cashback'] = true;
                    $reponse['total_cashback'] += $total_cashback;
                    array_push($reponse['data'], [
                        'promo_name' => $rule->getName(),
                        'amount' => $total_cashback
                    ]);
                }
            }
        }

        return $reponse;
    }
}
