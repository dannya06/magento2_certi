<?php
 
namespace Icube\PromoShipping\Plugin\Sales\Rule;
 
class ValueProviderPlugin
{    
            
    public function afterGetMetadataValues(\Magento\SalesRule\Model\Rule\Metadata\ValueProvider $subject, $result)
    {
        $addApplyOptions = [
            ['label' => __('Fixed Shipping Amount Discount'), 'value' => 'shipping_disc_by_amount'],
            ['label' => __('Percentage Shipping Amount Discount'), 'value' => 'shipping_disc_by_percent']
        ];

        $oldApplyOption = $result["actions"]["children"]["simple_action"]["arguments"]["data"]["config"]["options"];
        
        $newApplyOption = array_merge($addApplyOptions, $oldApplyOption);
        $result["actions"]["children"]["simple_action"]["arguments"]["data"]["config"]["options"] = $newApplyOption;

        return $result;
    }    
}
