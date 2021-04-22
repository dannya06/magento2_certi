<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Plugin\Quote\Model\Cart;

use Amasty\Extrafee\Api\TaxExtrafeeDetailsInterfaceFactory;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory as FeeQuoteCollectionFactory;
use Amasty\Extrafee\Model\Tax;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\Data\TotalSegmentExtensionFactory;
use Magento\Quote\Model\Cart\TotalsConverter;
use Magento\Checkout\Model\Session;

class TotalsConverterPlugin
{
    /**
     * @var TotalSegmentExtensionFactory
     */
    private $totalSegmentExtensionFactory;

    /**
     * @var FeeQuoteCollectionFactory
     */
    private $feeQuoteCollectionFactory;

    /**
     * @var string
     */
    private $code;

    /**
     * @var Tax
     */
    private $tax;
    /**
     * @var Session
     */
    private $session;

    /**
     * @var TaxExtrafeeDetailsInterfaceFactory
     */
    private $taxExtrafeeDetailsFactory;
    /**
     * @var Json
     */
    private $jsonSerializer;

    public function __construct(
        TotalSegmentExtensionFactory $totalSegmentExtensionFactory,
        FeeQuoteCollectionFactory $feeQuoteCollectionFactory,
        Tax $tax,
        Session $session,
        TaxExtrafeeDetailsInterfaceFactory $taxExtrafeeDetailsFactory,
        Json $jsonSerializer
    ) {
        $this->totalSegmentExtensionFactory = $totalSegmentExtensionFactory;
        $this->code = 'amasty_extrafee';
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        $this->tax = $tax;
        $this->session = $session;
        $this->taxExtrafeeDetailsFactory = $taxExtrafeeDetailsFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param TotalsConverter $subject
     * @param array $totalSegments
     * @param array $addressTotals
     * @return array
     */
    public function afterProcess(
        TotalsConverter $subject,
        array $totalSegments,
        array $addressTotals = []
    ) {
        if (!array_key_exists($this->code, $addressTotals)) {
            return $totalSegments;
        }

        $quote = $this->session->getQuote();
        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('option_id', ['neq' => '0'])
            ->addFieldToFilter('quote_id', $quote->getId());
        $taxExtrafeeDetails = $this->taxExtrafeeDetailsFactory->create();
        $taxExtrafeeDetails->setValueInclTax($addressTotals[$this->code]['value_incl_tax']);
        $taxExtrafeeDetails->setValueExclTax($addressTotals[$this->code]['value_excl_tax']);
        $iterator = 0;
        foreach ($feesQuoteCollection->getItems() as $key => $feeOption) {
            $taxDetails['items'][$iterator]['amount'] = $feeOption->getFeeAmount();
            $taxDetails['items'][$iterator]['base_amount'] = $feeOption->getBaseFeeAmount();
            $taxDetails['items'][$iterator]['labels'] = $feeOption->getLabel();
            $taxDetails['items'][$iterator]['amount_incl_tax'] = $feeOption->getFeeAmount()
                + $feeOption->getTaxAmount();
            $taxDetails['items'][$iterator]['amount_excl_tax'] = $feeOption->getFeeAmount();
            $iterator++;
        }
        if (!empty($taxDetails['items'])) {
            $taxExtrafeeDetails->setItems($this->jsonSerializer->serialize($taxDetails['items']));
        }
        $attributes = $totalSegments[$this->code]->getExtensionAttributes();

        if ($attributes === null) {
            $attributes = $this->totalSegmentExtensionFactory->create();
        }
        $attributes->setTaxAmastyExtrafeeDetails($taxExtrafeeDetails);
        $totalSegments[$this->code]->setExtensionAttributes($attributes);

        return $totalSegments;
    }
}
