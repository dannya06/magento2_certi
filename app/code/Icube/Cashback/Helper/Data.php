<?php

namespace Icube\Cashback\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CASHBACK_FIXED = 'cashback_fixed';
    const CASHBACK_PERCENT = 'cashback_percent';

    public function getStaticCashbackTypes(){
        $types = [
            self::CASHBACK_FIXED => __('Cashback Fixed'),
            self::CASHBACK_PERCENT => __('Cashback Percent')

        ];

        return $types;
    }

    public function getCashbackTypes(){
        
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

}
