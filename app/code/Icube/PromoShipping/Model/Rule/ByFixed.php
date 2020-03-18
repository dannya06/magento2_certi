<?php

namespace Icube\PromoShipping\Model\Rule;

class ByFixed extends \Magento\SalesRule\Model\Rule\Action\Discount\ByFixed
{
    public function calculate($rule, $item, $qty)
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
