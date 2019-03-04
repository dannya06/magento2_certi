<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Model\Rule\Action\Discount;

use Magento\SalesRule\Model\Rule as RuleModel;

class SetofFixed extends AbstractSetof
{
    /**
     * @param RuleModel $rule
     *
     * @return $this
     */
    protected function calculateDiscountForRule($rule, $item)
    {
        list($setQty,$itemsForSet) = $this->prepareDataForCalculation($rule);

        if (!$itemsForSet) {
            return $this;
        }

        $totalPrice = $this->getItemsPrice($itemsForSet);
        $quoteAmount = $setQty * $rule->getDiscountAmount();

        if ($totalPrice < $quoteAmount) {
            foreach (self::$allItems as $i => $elem) {
                if ($item->getSku() == $elem->getSku()) {
                    unset(self::$allItems[$i]);
                }
            }

            return $this;
        }

        $countItemsForDiscount = count($itemsForSet);
        $this->calculateDiscountForItems($totalPrice, $rule, $itemsForSet, $countItemsForDiscount, $quoteAmount);

        foreach ($itemsForSet as $i => $item) {
            unset(self::$allItems[$i]);
        }

        return $this;
    }

    /**
     * @param float $totalPrice
     * @param RuleModel $rule
     * @param array $itemsForSet
     * @param int $maxDiscountQty
     * @param float|int $quoteAmount
     *
     * @return void
     */
    private function calculateDiscountForItems($totalPrice, $rule, $itemsForSet, $maxDiscountQty, $quoteAmount)
    {
        $ruleId = $this->getRuleId($rule);
        foreach ($itemsForSet as $item) {
            if ($maxDiscountQty > 0) {
                $discountData = $this->discountFactory->create();

                $baseItemPrice = $this->rulesProductHelper->getItemBasePrice($item);
                $baseItemOriginalPrice = $this->rulesProductHelper->getItemBaseOriginalPrice($item);

                $percentage = $baseItemPrice / $totalPrice;
                $baseDiscount = $baseItemPrice - $quoteAmount * $percentage;
                $itemDiscount = $this->priceCurrency->convert($baseDiscount, $item->getQuote()->getStore());
                $baseOriginalDiscount = $baseItemOriginalPrice - $quoteAmount * $percentage;
                $originalDiscount = ($baseItemOriginalPrice/$baseItemPrice) *
                    $this->priceCurrency->convert($baseOriginalDiscount, $item->getQuote()->getStore());

                if (!isset(self::$cachedDiscount[$ruleId][$item->getProductId()])) {
                    $discountData->setAmount($itemDiscount);
                    $discountData->setBaseAmount($baseDiscount);
                    $discountData->setOriginalAmount($originalDiscount);
                    $discountData->setBaseOriginalAmount($baseOriginalDiscount);
                } else {
                    $cachedItem = self::$cachedDiscount[$ruleId][$item->getProductId()];
                    $discountData->setAmount($itemDiscount + $cachedItem->getAmount());
                    $discountData->setBaseAmount($baseDiscount + $cachedItem->getBaseAmount());
                    $discountData->setOriginalAmount($originalDiscount + $cachedItem->getOriginalAmount());
                    $discountData->setBaseOriginalAmount($baseOriginalDiscount + $cachedItem->getBaseOriginalAmount());
                }
                $maxDiscountQty--;
                self::$cachedDiscount[$ruleId][$item->getProductId()] = $discountData;
            } else {
                break;
            }
        }
    }

    /**
     * @param array $items
     *
     * @return float
     */
    private function getItemsPrice($items)
    {
        $totalPrice = 0;
        foreach ($items as $item) {
            $totalPrice += $this->validator->getItemBasePrice($item);
        }

        return $totalPrice;
    }
}
