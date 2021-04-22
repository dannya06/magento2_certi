<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\Config\Source\Excludeinclude;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Tax\Model\Calculation;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;

class OptionManager
{
    const HUNDRED_PERCENT = 100;

    /**
     * @var Tax
     */
    private $tax;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Rule\RuleRepository
     */
    private $ruleRepository;

    /**
     * @var Calculation
     */
    private $calculationTool;

    public function __construct(
        Tax $tax,
        PriceCurrencyInterface $priceCurrency,
        ConfigProvider $configProvider,
        Calculation $calculationTool,
        Rule\RuleRepository $ruleRepository
    ) {
        $this->tax = $tax;
        $this->priceCurrency = $priceCurrency;
        $this->configProvider = $configProvider;
        $this->calculationTool = $calculationTool;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @param int $storeId
     * @param array $values
     *
     * @return string
     */
    public function getOptionLabel($storeId, array $values)
    {
        if (array_key_exists($storeId, $values) && $values[$storeId] !== '') {
            return $values[$storeId];
        }

        if (array_key_exists(0, $values)) {
            return $values[0];
        }

        return '';
    }

    /**
     * @param Quote $quote
     * @param FeeInterface $fee
     *
     * @return array
     */
    public function fetchBaseOptions(Quote $quote, FeeInterface $fee)
    {
        $options = [];
        $storeId = $quote->getStoreId();
        $taxesEnabled = $this->configProvider->getCalcMethod() == ConfigProvider::INCLUDE_TAX;
        $baseQuoteTotal = $baseQuoteTax = $qty = null;
        $taxRate = $taxesEnabled ? $this->tax->getTaxRate($quote) : 0;

        foreach ($fee->getOptions() as $item) {
            $tax = $baseTax = 0.0;
            $basePrice = $this->priceToFloat($item['price']);
            $needToCalcTax = $taxesEnabled;
            if ($item['price_type'] === Fee::PRICE_TYPE_PERCENT) {
                if ($baseQuoteTotal === null) {
                    list($baseQuoteTotal, $baseQuoteTax) = $this->getBaseQuoteTotalAndBaseTax($quote, $fee);
                }
                $percent = $basePrice;
                $basePrice = $baseQuoteTotal / self::HUNDRED_PERCENT * $percent;
                if ($taxesEnabled && !$this->configProvider->useFeeTaxClassForPercentFee()) {
                    $baseTax = $baseQuoteTax / self::HUNDRED_PERCENT * $percent;
                    $tax = $this->priceCurrency->convertAndRound($baseTax);
                    $needToCalcTax = false;
                }
            } elseif ($item['price_type'] === Fee::PRICE_TYPE_FIXED) {
                if (!$qty) {
                    $qty = 1;
                    if ($fee->isPerProduct()) {
                        $qty = $this->getValidQty($quote, $fee);
                    }
                }
                $basePrice *= $qty;
            }

            $price = $this->priceCurrency->convertAndRound($basePrice);

            if ($needToCalcTax && $taxRate) {
                $baseTax = $this->calculationTool->calcTaxAmount(
                    $basePrice,
                    $taxRate,
                    false,
                    false
                );

                $tax = $this->calculationTool->calcTaxAmount(
                    $price,
                    $taxRate,
                    false,
                    false
                );
            }

            $options[] = [
                'index' => $item['entity_id'],
                'price' => $price,
                'base_price' => $this->priceCurrency->round($basePrice),
                'tax' => $tax,
                'base_tax' => $baseTax,
                'default' => $item['default'],
                'value_incl_tax' => $price + $tax,
                'value_excl_tax' => $price,
                'label' => $this->getOptionLabel($storeId, $item['options'])
            ];
        }

        return $options;
    }

    /**
     * @param string $price
     *
     * @return float
     */
    private function priceToFloat($price)
    {
        // convert "," to "."
        $price = str_replace(',', '.', $price);
        // remove everything except numbers and dot "."
        $price = preg_replace("/[^0-9\.]/", "", $price);
        // remove all seperators from first part and keep the end
        $price = str_replace('.', '', substr($price, 0, -3)) . substr($price, -3);

        return (float)$price;
    }

    /**
     * @param Quote $quote
     * @param FeeInterface $fee
     *
     * @return array Format: [floatBaseTotal, floatBaseTax]
     */
    private function getBaseQuoteTotalAndBaseTax(Quote $quote, FeeInterface $fee)
    {
        $baseTotal = $baseTax = 0.0;
        $items = $this->getValidItems($quote, $fee);

        if (empty($items)) {
            return [$baseTotal, $baseTax];
        }

        foreach ($items as $item) {
            $itemQty = $item->getQty();

            if ($item->getProductType() !== Type::TYPE_BUNDLE) {
                $parent = $item->getParentItem();
                if ($parent) {
                    $itemQty = $parent->getQty();
                    if ($parent->getProductType() === Type::TYPE_BUNDLE) {
                        $itemQty *= $item->getQty();
                    }
                }
                $baseTotal += $item->getBasePrice() * $itemQty;
                $baseTax += $item->getBaseTaxAmount() - $item->getBaseDiscountTaxCompensation();
            }

            if ($this->getDiscountInSubtotal($fee)) {
                $baseTotal -= $item->getBaseDiscountAmount();
            }
        }

        if ($this->getShippingInSubtotal($fee) && !$quote->isVirtual()) {
            $baseTotal += $quote->getShippingAddress()->getBaseShippingAmount();
            $baseTax += $quote->getShippingAddress()->getBaseShippingTaxAmount();
        }

        return [$baseTotal, $baseTax];
    }

    /**
     * @param Quote $quote
     * @param FeeInterface $fee
     *
     * @return float|int
     */
    private function getValidQty(Quote $quote, FeeInterface $fee)
    {
        $items = $this->getValidItems($quote, $fee);
        $qty = 0;
        foreach ($items as $item) {
            if (!$item->getParentItem()) {
                $qty += $item->getQty();
            }
        }

        return $qty;
    }

    /**
     * @param Quote $quote
     * @param FeeInterface $fee
     *
     * @return Item[]
     */
    private function getValidItems(Quote $quote, FeeInterface $fee)
    {
        /** @var Item[] $items */
        if (!$fee->isPerProduct()) {
            return $quote->getAllItems();
        }

        $rule = $this->ruleRepository->getByFee($fee);

        return $rule->getValidItems($quote);
    }

    /**
     * @param FeeInterface $fee
     *
     * @return int
     */
    private function getDiscountInSubtotal(FeeInterface $fee)
    {
        $value = $fee->getDiscountInSubtotal();

        if ($value === Excludeinclude::VAR_DEFAULT) {
            $value = $this->configProvider->getDiscountInSubtotal();
        }

        return $value;
    }

    /**
     * @param FeeInterface $fee
     *
     * @return int
     */
    private function getShippingInSubtotal(FeeInterface $fee)
    {
        $value = $fee->getShippingInSubtotal();

        if ($value === Excludeinclude::VAR_DEFAULT) {
            $value = $this->configProvider->getShippingInSubtotal();
        }

        return $value;
    }
}
