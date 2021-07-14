<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class Tax
{
    /**
     * @var Calculation
     */
    private $calculation;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Calculation $calculation,
        ConfigProvider $configProvider
    ) {
        $this->calculation = $calculation;
        $this->configProvider = $configProvider;
    }

    /**
     * @param Quote $quote
     *
     * @return \Magento\Framework\DataObject|null
     */
    private function getRateRequest(Quote $quote)
    {
        if ($taxClass = $this->configProvider->getTaxClass()) {
            return $this->calculation->getRateRequest(
                $quote->getShippingAddress(),
                $quote->getBillingAddress(),
                $quote->getCustomerTaxClassId(),
                $quote->getStore(),
                $quote->getCustomerId()
            )->setProductClassId($taxClass);
        }

        return null;
    }

    /**
     * @param Quote $quote
     *
     * @return float
     */
    public function getTaxRate(Quote $quote)
    {
        $rateRequest = $this->getRateRequest($quote);
        $rate = 0;

        if ($rateRequest) {
            $rate = $this->calculation->getRate($rateRequest);
        }

        return $rate;
    }

    /**
     * @param AddressInterface $address
     * @param float $feeAmountWithTax
     * @param float $baseFeeAmountWithTax
     */
    public function addFeeTax($address, $feeAmountWithTax, $baseFeeAmountWithTax)
    {
        $taxClass = $this->configProvider->getTaxClass();
        $associatedTaxables = $address->getAssociatedTaxables();
        if (!$associatedTaxables) {
            $associatedTaxables = [];
        }

        $associatedTaxables[] = [
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TYPE => 'fee',
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_CODE => 'fee',
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $feeAmountWithTax,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $baseFeeAmountWithTax,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_QUANTITY => 1,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $taxClass,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_PRICE_INCLUDES_TAX => true,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_ASSOCIATION_ITEM_CODE
            => CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE,
        ];

        $address->setAssociatedTaxables($associatedTaxables);
    }

    /**
     * @param array $appliedTaxes
     * @param float $taxAmount
     * @param float $baseTaxAmount
     *
     * @return array
     */
    public function applyToExisting($appliedTaxes, $taxAmount, $baseTaxAmount)
    {
        foreach ($appliedTaxes as &$appliedTax) {
            $appliedTax['amount'] += $taxAmount;
            $appliedTax['base_amount'] += $baseTaxAmount;
            break;
        }

        return $appliedTaxes;
    }

    public function getTaxBreakdown(\Magento\Quote\Model\Quote $quote, $appliedTax, $taxAmount, $baseTaxAmount)
    {
        $rateRequest = $this->getRateRequest($quote);

        if ($rateRequest) {
            $feeAppliedTax = current($this->calculation->getAppliedRates($rateRequest));

            if (isset($feeAppliedTax['percent'], $feeAppliedTax['id'])) {
                if (isset($appliedTax[$feeAppliedTax['id']])) {
                    $appliedTax[$feeAppliedTax['id']]['amount'] += $taxAmount;
                    $appliedTax[$feeAppliedTax['id']]['base_amount'] += $baseTaxAmount;
                } else {
                    $appliedTax[$feeAppliedTax['id']] = [
                        'amount' => $taxAmount,
                        'base_amount' => $baseTaxAmount,
                        'percent' => $feeAppliedTax['percent'],
                        'id' => $feeAppliedTax['id'],
                        'rates' => current($feeAppliedTax),
                        'item_id' => null,
                        'item_type' => null,
                        'associated_item_id' => null,
                        'process' => 0
                    ];
                }
            }
        }

        return $appliedTax;
    }
}
