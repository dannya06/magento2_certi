<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\Order\Total\Creditmemo;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\CollectionFactory as FeeOrderCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\FloatComparator;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Fee extends AbstractTotal
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var FeeOrderCollectionFactory
     */
    private $feeOrderCollectionFactory;

    /**
     * @var FloatComparator
     */
    private $floatComparator;

    public function __construct(
        RequestInterface $request,
        PriceCurrencyInterface $priceCurrency,
        FeeOrderCollectionFactory $feeOrderCollectionFactory,
        FloatComparator $floatComparator,
        array $data = []
    ) {
        $this->request = $request;
        $this->priceCurrency = $priceCurrency;
        $this->feeOrderCollectionFactory = $feeOrderCollectionFactory;
        $this->floatComparator = $floatComparator;
        parent::__construct($data);
    }

    /**
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $this->collectFee($creditmemo);

        return $this;
    }

    /**
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collectFee(Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $creditmemoPost = $this->request->getParam('creditmemo');

        $baseFeeAmount = $feeAmount = 0;
        $baseTaxAmount = $taxAmount = 0;
        $baseOriginTax = $originTax = 0;

        $feeOrderCollection = $this->feeOrderCollectionFactory->create();
        $feeOrderCollection->addFilterByOrderId($order->getId());
        $feeOrderCollection->joinFees();
        $isFeeTaxIncludedToGrandTotal = $creditmemo->isLast();

        /** @var \Amasty\Extrafee\Model\ExtrafeeOrder $feeOrder */
        foreach ($feeOrderCollection->getItems() as $feeOrder) {
            // grand total already contains all taxes, so we need to remove unrefundable fee taxes
            if (!$feeOrder->getData(FeeInterface::IS_ELIGIBLE_REFUND)) {
                $baseOriginTax += $feeOrder->getBaseTaxAmount();
                $originTax += $feeOrder->getTaxAmount();
                continue;
            }

            if (isset($creditmemoPost['extra_fee_' . $feeOrder->getFeeId() . '_' . $feeOrder->getOptionId()])) {
                // The logic uses the 'base' currency to be consistent with what the user (admin) provided as input
                $baseAllowedAmount = $feeOrder->getBaseTotalAmount() - $feeOrder->getBaseTotalAmountRefunded();
                $baseDesiredAmount
                    = (float)$creditmemoPost['extra_fee_' . $feeOrder->getFeeId() . '_' . $feeOrder->getOptionId()];
                $this->checkIsDesiredAmountAllowed($order, $baseDesiredAmount, $baseAllowedAmount);

                // compute of all amounts based on the desired amount
                $ratio = $baseDesiredAmount / $feeOrder->getBaseTotalAmount();
                if ($ratio != 1) {
                    $desiredAmount = $this->priceCurrency->round($feeOrder->getTotalAmount() * $ratio);
                    $baseDesiredAmount = $this->priceCurrency->round($feeOrder->getBaseTotalAmount() * $ratio);
                    $desiredTaxAmount = $this->priceCurrency->round($feeOrder->getTaxAmount() * $ratio);
                    $baseDesiredTaxAmount = $this->priceCurrency->round($feeOrder->getBaseTaxAmount() * $ratio);
                } else {
                    $desiredAmount = $feeOrder->getTotalAmount();
                    $baseDesiredAmount = $feeOrder->getBaseTotalAmount();
                    $desiredTaxAmount = $feeOrder->getTaxAmount();
                    $baseDesiredTaxAmount = $feeOrder->getBaseTaxAmount();
                }

                $feeAmount += $desiredAmount;
                $baseFeeAmount += $baseDesiredAmount;

                $originTax += $feeOrder->getTaxAmount() - $feeOrder->getTaxAmountRefunded();
                $baseOriginTax += $feeOrder->getBaseTaxAmount() - $feeOrder->getBaseTaxAmountRefunded();
                $taxAmount += $desiredTaxAmount;
                $baseTaxAmount += $baseDesiredTaxAmount;
            } else {
                $feeAmount += $feeOrder->getTotalAmount() - $feeOrder->getTotalAmountRefunded();
                $baseFeeAmount += $feeOrder->getBaseTotalAmount() - $feeOrder->getBaseTotalAmountRefunded();
            }
        }

        if (!$isFeeTaxIncludedToGrandTotal) {
            $baseOriginTax = $originTax = 0;
        }

        // $creditmemo->getGrandTotal() already contains fee tax but does not contain a fee amount
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $feeAmount - $originTax + $taxAmount);
        $creditmemo->setBaseGrandTotal(
            $creditmemo->getBaseGrandTotal() + $baseFeeAmount - $baseOriginTax + $baseTaxAmount
        );
        // $creditmemo->getTaxAmount() already contains fee tax
        $creditmemo->setTaxAmount($creditmemo->getTaxAmount() - $originTax + $taxAmount);
        $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() - $baseOriginTax + $baseTaxAmount);

        return $this;
    }

    /**
     * @param Order $order
     * @param float $desiredAmount
     * @param float $baseAllowedAmount
     * @throws LocalizedException
     */
    public function checkIsDesiredAmountAllowed(Order $order, $desiredAmount, $baseAllowedAmount)
    {
        if ($this->floatComparator->greaterThan($desiredAmount, $baseAllowedAmount)) {
            $baseAllowedAmount = $order->getBaseCurrency()->format($baseAllowedAmount, null, false);
            throw new LocalizedException(
                __('Maximum fee amount allowed to refund is: %1', $baseAllowedAmount)
            );
        }
    }
}
