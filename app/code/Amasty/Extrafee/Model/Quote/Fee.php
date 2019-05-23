<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */

namespace Amasty\Extrafee\Model\Quote;

/**
 * Class Fee
 *
 * @author Artem Brunevski
 */

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Amasty\Extrafee\Model\ResourceModel\Quote\CollectionFactory as FeeQuoteCollectionFactory;
use Amasty\Extrafee\Model\TotalsInformationManagement;
use Amasty\Extrafee\Model\Tax;
use Magento\Store\Model\StoreManagerInterface;

class Fee extends AbstractTotal
{
    /** @var FeeQuoteCollectionFactory  */
    protected $feeQuoteCollectionFactory;

    /** @var  array */
    protected $jsonLabels = [];

    /** @var  float */
    protected $feeAmount;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var TotalsInformationManagement  */
    protected $totalsInformationManagement;

    /**
     * @var \Amasty\Extrafee\Model\Tax
     */
    private $tax;

    public function __construct(
        FeeQuoteCollectionFactory $feeQuoteCollectionFactory,
        StoreManagerInterface $storeManager,
        TotalsInformationManagement $totalsInformationManagement,
        Tax $tax
    ) {
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        $this->totalsInformationManagement = $totalsInformationManagement;
        $this->storeManager = $storeManager;
        $this->tax = $tax;
    }

    /**
     * If current currency code of quote is not equal current currency code of store,
     * need recalculate fees of quote. It is possible if customer use currency switcher or
     * store switcher.
     * @param Quote $quote
     */
    protected function checkCurrencyCode(Quote $quote)
    {
        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quote->getId());

        if ($quote->getQuoteCurrencyCode() !== $this->storeManager->getStore()->getCurrentCurrencyCode()) {
            foreach($feesQuoteCollection as $feeQuote){
                $feeQuote->delete();
            }
        }
    }

    /**
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
        $total->setTotalAmount($this->getCode(), 0);
        $total->setBaseTotalAmount($this->getCode(), 0);

        $this->totalsInformationManagement->updateQuoteFees($quote);

        $this->jsonLabels = [];
        $this->checkCurrencyCode($quote);

        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('option_id', ['neq' => '0'])
            ->addFieldToFilter('quote_id', $quote->getId());

        $feeAmount = 0;
        $baseFeeAmount = 0;

        foreach($feesQuoteCollection as $feeOption) {
            $feeAmount += $feeOption->getFeeAmount();
            $baseFeeAmount += $feeOption->getBaseFeeAmount();
            $this->jsonLabels[] = $feeOption->getLabel();
        }

        $total->setTotalAmount($this->getCode(), $feeAmount);
        $total->setBaseTotalAmount($this->getCode(), $baseFeeAmount);

        $this->feeAmount = $feeAmount;

        $address = $shippingAssignment->getShipping()->getAddress();

        $this->tax->addFeeTax($address, $feeAmount, $baseFeeAmount);

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, Total $total)
    {
        if ($this->jsonLabels) {
            return [
                'code' => 'amasty_extrafee',
                'title' => __('Extra Fee (%1)', implode(', ', $this->jsonLabels)),
                'value' => $this->feeAmount
            ];
        }
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Amasty Fee');
    }
}
