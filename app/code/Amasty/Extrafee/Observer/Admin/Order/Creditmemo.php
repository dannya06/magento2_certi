<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Observer\Admin\Order;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Api\ExtrafeeOrderRepositoryInterface;
use Amasty\Extrafee\Api\ExtrafeeCreditmemoRepositoryInterface;
use Amasty\Extrafee\Model\ExtrafeeCreditmemoFactory;
use Amasty\Extrafee\Model\FeeEligibleDataProvider;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\Collection;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\CollectionFactory as ExtrafeeOrderCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Math\FloatComparator;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

/**
 * save extra fee data when submit credit memo, 'sales_order_creditmemo_refund' event
 */
class Creditmemo implements ObserverInterface
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
     * @var ExtrafeeCreditmemoFactory
     */
    private $extrafeeCreditmemoFactory;

    /**
     * @var ExtrafeeOrderRepositoryInterface
     */
    private $extrafeeOrderRepository;

    /**
     * @var ExtrafeeOrderCollectionFactory
     */
    private $extrafeeOrderCollectionFactory;

    /**
     * @var ExtrafeeCreditmemoRepositoryInterface
     */
    private $extrafeeCreditmemoRepository;

    /**
     * @var FloatComparator
     */
    private $floatComparator;

    /**
     * @var FeeEligibleDataProvider
     */
    private $feeEligibleProvider;

    public function __construct(
        RequestInterface $request,
        PriceCurrencyInterface $priceCurrency,
        ExtrafeeCreditmemoFactory $extrafeeCreditmemoFactory,
        ExtrafeeOrderRepositoryInterface $extrafeeOrderRepository,
        ExtrafeeOrderCollectionFactory $extrafeeOrderCollectionFactory,
        ExtrafeeCreditmemoRepositoryInterface $extrafeeCreditmemoRepository,
        FloatComparator $floatComparator,
        FeeEligibleDataProvider $feeEligibleProvider
    ) {
        $this->request = $request;
        $this->priceCurrency = $priceCurrency;
        $this->extrafeeCreditmemoFactory = $extrafeeCreditmemoFactory;
        $this->extrafeeOrderRepository = $extrafeeOrderRepository;
        $this->extrafeeOrderCollectionFactory = $extrafeeOrderCollectionFactory;
        $this->extrafeeCreditmemoRepository = $extrafeeCreditmemoRepository;
        $this->floatComparator = $floatComparator;
        $this->feeEligibleProvider = $feeEligibleProvider;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $creditmemoPost = $this->request->getParam('creditmemo');
        $orderId = $creditmemo->getOrderId();
        $ineligibleFeesBaseTotalAmount = 0.0;
        $feeEligibleIds = $this->feeEligibleProvider->getEligibleIdsForRefund();
        $extrafeeOrderCollection = $this->getFeeOrderCollection($orderId);

        /** @var \Amasty\Extrafee\Model\ExtrafeeOrder $feeOrder */
        foreach ($extrafeeOrderCollection->getItems() as $feeOrder) {
            if (isset($creditmemoPost['extra_fee_' . $feeOrder->getFeeId() . '_' . $feeOrder->getOptionId()])
                && in_array($feeOrder->getFeeId(), $feeEligibleIds)
            ) {
                $baseDesiredAmount
                    = (float)$creditmemoPost['extra_fee_' . $feeOrder->getFeeId() . '_' . $feeOrder->getOptionId()];

                $ratio = $baseDesiredAmount / $feeOrder->getBaseTotalAmount();
                $desiredAmount = (float)$this->priceCurrency->round($feeOrder->getTotalAmount() * $ratio);
                $baseDesiredAmount = (float)$this->priceCurrency->round($feeOrder->getBaseTotalAmount() * $ratio);
                $desiredTaxAmount = (float)$this->priceCurrency->round($feeOrder->getTaxAmount() * $ratio);
                $baseDesiredTaxAmount = (float)$this->priceCurrency->round($feeOrder->getBaseTaxAmount() * $ratio);

                $feeOrder->setTotalAmountRefunded($feeOrder->getTotalAmountRefunded() + $desiredAmount);
                $feeOrder->setBaseTotalAmountRefunded($feeOrder->getBaseTotalAmountRefunded() + $baseDesiredAmount);
                $feeOrder->setTaxAmountRefunded($feeOrder->getTaxAmountRefunded() + $desiredTaxAmount);
                $feeOrder->setBaseTaxAmountRefunded($feeOrder->getBaseTaxAmountRefunded() + $baseDesiredTaxAmount);

                if ($feeOrder->getBaseTotalAmount() <= $feeOrder->getBaseTotalAmountRefunded()) {
                    $feeOrder->setIsRefunded(true);
                }

                $this->extrafeeOrderRepository->save($feeOrder);

                $this->createExtrafeeCreditMemo(
                    $feeOrder,
                    $orderId,
                    $creditmemo->getId(),
                    $desiredAmount,
                    $baseDesiredAmount,
                    $desiredTaxAmount,
                    $baseDesiredTaxAmount
                );
            } else {
                $ineligibleFeesBaseTotalAmount += $feeOrder->getBaseTotalAmount();
                $ineligibleFeesBaseTotalAmount += $feeOrder->getBaseTaxAmount();
            }
        }
        $this->changeOrderStatus($creditmemo->getOrder(), $ineligibleFeesBaseTotalAmount);
    }

    /**
     * @param ExtrafeeOrderInterface $extrafeeOrder
     * @param int $orderId
     * @param int $creditmemoId
     * @param float $desiredAmount
     * @param float $baseDesiredAmount
     * @param float $desiredTaxAmount
     * @param float $baseDesiredTaxAmount
     */
    private function createExtrafeeCreditMemo(
        ExtrafeeOrderInterface $extrafeeOrder,
        $orderId,
        $creditmemoId,
        $desiredAmount,
        $baseDesiredAmount,
        $desiredTaxAmount,
        $baseDesiredTaxAmount
    ) {
        $extrafeeCreditmemo = $this->extrafeeCreditmemoFactory->create();

        $extrafeeCreditmemo->setOrderId((int)$orderId);
        $extrafeeCreditmemo->setCreditmemoId((int)$creditmemoId);
        $extrafeeCreditmemo->setFeeId((int)$extrafeeOrder->getFeeId());
        $extrafeeCreditmemo->setOptionId((int)$extrafeeOrder->getOptionId());
        $extrafeeCreditmemo->setBaseTotalAmount((float)$baseDesiredAmount);
        $extrafeeCreditmemo->setTotalAmount((float)$desiredAmount);
        $extrafeeCreditmemo->setBaseTaxAmount((float)$baseDesiredTaxAmount);
        $extrafeeCreditmemo->setTaxAmount((float)$desiredTaxAmount);
        $extrafeeCreditmemo->setFeeLabel($extrafeeOrder->getLabel());
        $extrafeeCreditmemo->setFeeOptionLabel($extrafeeOrder->getOptionLabel());

        $this->extrafeeCreditmemoRepository->save($extrafeeCreditmemo);
    }

    /**
     * @param int $orderId
     * @return Collection
     */
    private function getFeeOrderCollection($orderId)
    {
        return $this->extrafeeOrderCollectionFactory->create()
            ->addFilterByOrderId($orderId)
            ->addFieldToFilter(ExtrafeeOrderInterface::IS_REFUNDED, 0);
    }

    /**
     * @param Order $order
     * @param float $ineligibleFeesBaseTotalAmount
     */
    private function changeOrderStatus(Order $order, float $ineligibleFeesBaseTotalAmount)
    {
        $nonRefundedBalance = $order->getBaseTotalPaid() - $order->getBaseTotalRefunded();
        if ($this->floatComparator->equal($nonRefundedBalance, $ineligibleFeesBaseTotalAmount)
            && $ineligibleFeesBaseTotalAmount != 0
        ) {
            $order->setState(Order::STATE_CANCELED)
                ->setStatus(Order::STATE_CANCELED);
            foreach ($order->getStatusHistories() as $item) {
                $item->setStatus(Order::STATE_CANCELED);
            }
        }
    }
}
