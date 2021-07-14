<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\Quote;

use Amasty\Extrafee\Api\FeesInformationManagementInterface;
use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory as FeeQuoteCollectionFactory;
use Amasty\Extrafee\Model\Tax;
use Amasty\Extrafee\Model\TotalsInformationManagement;
use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Config;

/**
 * Fee total collector
 */
class Fee extends AbstractTotal
{
    /**
     * @var FeeQuoteCollectionFactory
     */
    private $feeQuoteCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TotalsInformationManagement
     */
    private $totalsInformationManagement;

    /**
     * @var Tax
     */
    private $tax;

    /**
     * @var FeesInformationManagementInterface
     */
    private $feesInformationManagement;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Calculation
     */
    private $calculationTool;

    public function __construct(
        FeeQuoteCollectionFactory $feeQuoteCollectionFactory,
        StoreManagerInterface $storeManager,
        TotalsInformationManagement $totalsInformationManagement,
        Tax $tax,
        FeesInformationManagementInterface $feesInformationManagement,
        ConfigProvider $configProvider,
        Calculation $calculationTool
    ) {
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        $this->totalsInformationManagement = $totalsInformationManagement;
        $this->storeManager = $storeManager;
        $this->tax = $tax;
        $this->feesInformationManagement = $feesInformationManagement;
        $this->configProvider = $configProvider;
        $this->calculationTool = $calculationTool;
    }

    /**
     * Collect totals process.
     * Assign Fee amount to Total object
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        if (!$shippingAssignment->getItems()) {
            return $this;
        }

        if (!$quote->getAppliedAmastyFeeFlag()) {
            // load default fees or quote fees and delete invalid quote fees
            $this->feesInformationManagement->collectQuote($quote);
        }

        // apply the selected fee and apply tax
        $this->totalsInformationManagement->updateQuoteFees($quote);

        //get total amount for all options
        $feesData = $this->getFeesData($quote);

        $this->_setAmount($feesData['amount']);
        $this->_setBaseAmount($feesData['base_amount']);

        $taxesEnabled = $this->configProvider->getCalcMethod() == ConfigProvider::INCLUDE_TAX;
        if ($taxesEnabled) {
            // @TODO: bad implementation. Need to sum all fixed fees and percent fees with our tax class and add
            // one associated taxable row. For percent fees which didn't use our tax class we need to add associated
            // taxable row for each product tax class id in quote, and row for shipping tax class id if we need to calc
            // include shipping ($this->tax->addFeeTax method)
            //
            // if there is an applied taxes - we add our amount to it
            // if no - it means, that we calculated taxes based on our tax class and we add new tax row to applied taxes
            $appliedTaxes = $total->getData('applied_taxes');
            if ($appliedTaxes) {
                $appliedTaxes = $this->tax->applyToExisting(
                    $appliedTaxes,
                    $feesData['tax_amount'],
                    $feesData['base_tax_amount']
                );
            } else {
                $appliedTaxes = $this->tax->getTaxBreakdown(
                    $quote,
                    [],
                    $feesData['tax_amount'],
                    $feesData['base_tax_amount']
                );
            }

            if ($appliedTaxes) {
                $total->setTotalAmount('tax', $total->getTotalAmount('tax') + $feesData['tax_amount']);
                $total->setBaseTotalAmount(
                    'tax',
                    $total->getBaseTotalAmount('tax') + $feesData['base_tax_amount']
                );
            }

            $total->setData('applied_taxes', $appliedTaxes);
        }

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Total $total
     * @return array|null
     */
    public function fetch(Quote $quote, Total $total)
    {
        $feesData = $this->getFeesData($quote);
        $amountInclTax = $feesData['amount'] + $feesData['tax_amount'];
        $amountExclTax = $feesData['amount'];

        return [
            'code' => $this->getCode(),
            'title' => __('Extra Fee (%1)', implode(', ', $feesData['labels'])),
            //value (in summary)
            'value' => $this->configProvider->displayCartPrices() == Config::DISPLAY_TYPE_EXCLUDING_TAX
                ? $amountExclTax
                : $amountInclTax,
            'value_incl_tax' => $amountInclTax,
            'value_excl_tax' => $amountExclTax,
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return Phrase
     */
    public function getLabel()
    {
        return __('Amasty Fee');
    }

    /**
     * Get ExtrafeeQuote Fees
     *
     * @param Quote $quote
     * @return array
     */
    private function getFeesData(Quote $quote)
    {
        $feesData = [
            'labels' => [],
            'amount' => 0,
            'base_amount' => 0,
            'tax_amount' => 0,
            'base_tax_amount' => 0
        ];

        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('option_id', ['neq' => '0'])
            ->addFieldToFilter('quote_id', $quote->getId());

        /** @var \Amasty\Extrafee\Model\ExtrafeeQuote $feeOption */

        //plus values for options quote
        foreach ($feesQuoteCollection->getItems() as $key => $feeOption) {
            $feesData['amount'] += $feeOption->getFeeAmount();
            $feesData['base_amount'] += $feeOption->getBaseFeeAmount();
            $feesData['tax_amount'] += $feeOption->getTaxAmount();
            $feesData['base_tax_amount'] += $feeOption->getBaseTaxAmount();
            $feesData['labels'][$key] = $feeOption->getLabel();
        }

        return $feesData;
    }
}
