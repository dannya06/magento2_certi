<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class Tax
{
    /**
     * @var \Magento\Tax\Model\Calculation
     */
    private $calculation;

    /**
     * @var \Amasty\Extrafee\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Tax\Model\Calculation $calculation,
        \Amasty\Extrafee\Helper\Data $helper
    ) {
        $this->calculation = $calculation;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Magento\Framework\DataObject|null
     */
    private function getRateRequest(\Magento\Quote\Model\Quote $quote)
    {
        $taxClass = $this->helper->getScopeValue('tax/tax_class');

        if ($taxClass) {
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
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return float
     */
    public function getTaxRate(\Magento\Quote\Model\Quote $quote)
    {
        $rateRequest = $this->getRateRequest($quote);
        $rate = 0;

        if ($rateRequest) {
            $rate = $this->calculation->getRate($rateRequest);
        }

        return $rate;
    }

    /**
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param float $feeAmount
     * @param float $baseFeeAmount
     */
    public function addFeeTax($address, $feeAmount, $baseFeeAmount)
    {
        $taxClass = $this->helper->getScopeValue('tax/tax_class');
        $associatedTaxables = $address->getAssociatedTaxables();
        if (!$associatedTaxables) {
            $associatedTaxables = [];
        }

        $associatedTaxables[] = [
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TYPE => 'fee',
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_CODE => 'fee',
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $feeAmount,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $baseFeeAmount,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_QUANTITY => 1,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $taxClass,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_PRICE_INCLUDES_TAX => false,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_ASSOCIATION_ITEM_CODE
            => CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE,
        ];

        $address->setAssociatedTaxables($associatedTaxables);
    }
}
