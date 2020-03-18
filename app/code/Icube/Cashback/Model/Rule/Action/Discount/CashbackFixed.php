<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icube\Cashback\Model\Rule\Action\Discount;

class CashbackFixed extends \Magento\SalesRule\Model\Rule\Action\Discount\AbstractDiscount
{
	/**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function calculate($rule, $item, $qty){
    	/** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        $store = $item->getQuote()->getStore();

        $itemPrice = $this->validator->getItemPrice($item);
        $baseItemPrice = $this->validator->getItemBasePrice($item);
        $itemOriginalPrice = $this->validator->getItemOriginalPrice($item);
        $baseItemOriginalPrice = $this->validator->getItemBaseOriginalPrice($item);

        $quoteAmount = $this->priceCurrency->convert($rule->getDiscountAmount(), $store);

        //amount set to 0 so it doesnt extract subtotal
        $discountData->setAmount(0);
        $discountData->setBaseAmount(0);
        $discountData->setOriginalAmount(0);
        $discountData->setBaseOriginalAmount(0);

        return $discountData;
    }
}
