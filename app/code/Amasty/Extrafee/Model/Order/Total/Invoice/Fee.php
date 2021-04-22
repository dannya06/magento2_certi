<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\Order\Total\Invoice;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\CollectionFactory as FeeOrderCollectionFactory;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory as FeeQuoteCollectionFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Fee extends AbstractTotal
{
    /**
     * @var FeeOrderCollectionFactory
     */
    private $feeOrderCollectionFactory;

    /**
     * @var FeeQuoteCollectionFactory
     */
    private $feeQuoteCollectionFactory;

    public function __construct(
        FeeOrderCollectionFactory $feeOrderCollectionFactory,
        FeeQuoteCollectionFactory $feeQuoteCollectionFactory,
        array $data = []
    ) {
        $this->feeOrderCollectionFactory = $feeOrderCollectionFactory;
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        parent::__construct($data);
    }

    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $this->collectFee($invoice);

        return $this;
    }

    /**
     * @param Invoice $invoice
     * @return $this
     */
    private function collectFee(Invoice $invoice)
    {
        if ($orderId = $invoice->getOrder()->getId()) {
            $totals = $this->collectFeeByOrderId($invoice->isLast(), $orderId);
        } else {
            $totals = $this->collectFeeByQuoteId($invoice->getOrder()->getQuoteId());
        }

        $invoice->setGrandTotal($invoice->getGrandTotal() + $totals['fee_amount'] + $totals['tax_amount']);
        $invoice->setBaseGrandTotal(
            $invoice->getBaseGrandTotal() + $totals['base_fee_amount'] + $totals['base_tax_amount']
        );
        $invoice->setTaxAmount($invoice->getTaxAmount() + $totals['tax_amount']);
        $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount() + $totals['base_tax_amount']);

        return $this;
    }

    /**
     * @param bool $isLastInvoice
     * @param int $orderId
     * @return array
     */
    private function collectFeeByOrderId($isLastInvoice, $orderId)
    {
        $feeAmount = 0;
        $baseFeeAmount = 0;
        $taxAmount = 0;
        $baseTaxAmount = 0;

        $feeOrderCollection = $this->feeOrderCollectionFactory->create()
            ->addFieldToFilter(ExtrafeeOrderInterface::ORDER_ID, $orderId);

        /** @var \Amasty\Extrafee\Model\ExtrafeeOrder $feeOrder */
        foreach ($feeOrderCollection->getItems() as $feeOrder) {
            if ($feeOrder->getBaseTotalAmountInvoiced() == 0) {
                $feeAmount += $feeOrder->getTotalAmount();
                $baseFeeAmount += $feeOrder->getBaseTotalAmount();
                if (!$isLastInvoice) {
                    $taxAmount += $feeOrder->getTaxAmount();
                    $baseTaxAmount += $feeOrder->getBaseTaxAmount();
                }
            }
        }

        return [
            'fee_amount' => $feeAmount,
            'base_fee_amount' => $baseFeeAmount,
            'tax_amount' => $taxAmount,
            'base_tax_amount' => $baseTaxAmount,
        ];
    }

    /**
     * When order id does not exist yet (authorize and capture)
     *
     * @param int $quoteId
     * @return array
     */
    private function collectFeeByQuoteId($quoteId)
    {
        $feeAmount = 0;
        $baseFeeAmount = 0;
        $taxAmount = 0;
        $baseTaxAmount = 0;

        $feeQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter(ExtrafeeQuoteInterface::QUOTE_ID, $quoteId);

        /** @var \Amasty\Extrafee\Model\ExtrafeeQuote $feeQuote */
        foreach ($feeQuoteCollection->getItems() as $feeQuote) {
            $feeAmount += $feeQuote->getFeeAmount();
            $baseFeeAmount += $feeQuote->getBaseFeeAmount();
            $taxAmount += $feeQuote->getTaxAmount();
            $baseTaxAmount += $feeQuote->getBaseTaxAmount();
        }

        return [
            'fee_amount' => $feeAmount,
            'base_fee_amount' => $baseFeeAmount,
            'tax_amount' => $taxAmount,
            'base_tax_amount' => $baseTaxAmount,
        ];
    }
}
