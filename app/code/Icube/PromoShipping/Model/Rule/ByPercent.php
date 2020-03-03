<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icube\PromoShipping\Model\Rule;

class ByPercent extends \Magento\SalesRule\Model\Rule\Action\Discount\ByPercent
{
    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return Data
     */
    public function calculate($rule, $item, $qty)
    {
        $rulePercent = min(100, $rule->getDiscountAmount());
        $discountData = $this->_calculate($rule, $item, $qty, $rulePercent);

        return $discountData;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @param float $rulePercent
     * @return Data
     */
    protected function _calculate($rule, $item, $qty, $rulePercent)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sales-rule.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);

        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();
        
        /**
         * CUSTOM ICUBE
         * So promo ongkir won't show in summary checkout
         */
        $discountData->setAmount(0);
        $discountData->setBaseAmount(0);

        return $discountData;
    }
}
