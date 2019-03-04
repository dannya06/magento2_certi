<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Model\Rule\Action\Discount;

use Magento\SalesRule\Model\Rule as RuleModel;

class SetofPercent extends AbstractSetof
{
    /**
     * @param RuleModel $rule
     *
     * @return $this
     */
    protected function calculateDiscountForRule($rule, $item)
    {
        list($setQty, $itemsForSet) = $this->prepareDataForCalculation($rule);

        if (!$itemsForSet) {
            return $this;
        }

        $countItemsForDiscount = count($itemsForSet);
        $this->calculateDiscountForItems($rule, $itemsForSet, $countItemsForDiscount);

        foreach ($itemsForSet as $i => $item) {
            unset(self::$allItems[$i]);
        }

        return $this;
    }

    /**
     * @param RuleModel $rule
     * @param array $itemsForSet
     * @param int $maxDiscountQty
     *
     * @return void
     */
    private function calculateDiscountForItems($rule, $itemsForSet, $maxDiscountQty)
    {
        $ruleId = $this->getRuleId($rule);
        foreach ($itemsForSet as $item) {
            if ($maxDiscountQty > 0) {
                $discountData = $this->discountFactory->create();

                $baseItemPrice = $this->rulesProductHelper->getItemBasePrice($item);
                $baseItemOriginalPrice = $this->rulesProductHelper->getItemBaseOriginalPrice($item);

                $percentage = min(100, $rule->getDiscountAmount()) / 100;
                $baseDiscount = $baseItemPrice * $percentage;
                $itemDiscount = $this->priceCurrency->convert($baseDiscount, $item->getQuote()->getStore());
                $baseOriginalDiscount = $baseItemOriginalPrice * $percentage;
                $originalDiscount = $this->priceCurrency->convert($baseOriginalDiscount, $item->getQuote()->getStore());

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
}
