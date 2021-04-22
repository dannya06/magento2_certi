<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Observer\Sales\Model;

use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Amasty\Extrafee\Api\ExtrafeeOrderRepositoryInterface;
use Amasty\Extrafee\Model\ExtrafeeOrderFactory;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\CollectionFactory as ExtrafeeOrderCollectionFactory;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory as ExtrafeeQuoteCollectionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * for move data information from quote to order, 'sales_order_invoice_save_after' event
 */
class AfterSaveOrder implements ObserverInterface
{
    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    /**
     * @var ExtrafeeOrderFactory
     */
    private $extrafeeOrderFactory;

    /**
     * @var ExtrafeeOrderRepositoryInterface
     */
    private $extrafeeOrderRepository;

    /**
     * @var ExtrafeeOrderCollectionFactory
     */
    private $extrafeeOrderCollectionFactory;

    /**
     * @var ExtrafeeQuoteCollectionFactory
     */
    private $extrafeeQuoteCollectionFactory;

    public function __construct(
        FeeRepositoryInterface $feeRepository,
        ExtrafeeOrderFactory $extrafeeOrderFactory,
        ExtrafeeOrderRepositoryInterface $extrafeeOrderRepository,
        ExtrafeeOrderCollectionFactory $extrafeeOrderCollectionFactory,
        ExtrafeeQuoteCollectionFactory $extrafeeQuoteCollectionFactory
    ) {
        $this->feeRepository = $feeRepository;
        $this->extrafeeOrderFactory = $extrafeeOrderFactory;
        $this->extrafeeOrderRepository = $extrafeeOrderRepository;
        $this->extrafeeOrderCollectionFactory = $extrafeeOrderCollectionFactory;
        $this->extrafeeQuoteCollectionFactory = $extrafeeQuoteCollectionFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $orderId = $order->getId();

        if ($this->isNotExistFeeOrder($orderId)) {
            $extrafeeQuoteCollection = $this->extrafeeQuoteCollectionFactory->create()
                ->addFilterByQuoteId($order->getQuoteId())
                ->addFieldToFilter('base_fee_amount', ['gt' => 0]);

            /** @var \Amasty\Extrafee\Model\ExtrafeeQuote $feeQuote */
            foreach ($extrafeeQuoteCollection->getItems() as $feeQuote) {
                $fee = $this->feeRepository->getById($feeQuote->getFeeId());
                $this->createExtrafeeOrder($feeQuote, $orderId, $fee->getName());
            }
        }
    }

    /**
     * @param ExtrafeeQuoteInterface $feeQuote
     * @param int $orderId
     * @param string $feeLabel
     */
    public function createExtrafeeOrder(ExtrafeeQuoteInterface $feeQuote, $orderId, $feeLabel)
    {
        $extrafeeOrder = $this->extrafeeOrderFactory->create();

        $extrafeeOrder->setOrderId((int)$orderId);
        $extrafeeOrder->setFeeId((int)$feeQuote->getFeeId());
        $extrafeeOrder->setOptionId((int)$feeQuote->getOptionId());
        $extrafeeOrder->setBaseTotalAmount((float)$feeQuote->getBaseFeeAmount());
        $extrafeeOrder->setTotalAmount((float)$feeQuote->getFeeAmount());
        $extrafeeOrder->setBaseTaxAmount((float)$feeQuote->getBaseTaxAmount());
        $extrafeeOrder->setTaxAmount((float)$feeQuote->getTaxAmount());
        $extrafeeOrder->setLabel($feeLabel);
        $extrafeeOrder->setOptionLabel($feeQuote->getLabel());
        $extrafeeOrder->setIsRefunded(false);

        $this->extrafeeOrderRepository->save($extrafeeOrder);
    }

    /**
     * @param int $orderId
     * @return bool
     */
    private function isNotExistFeeOrder($orderId)
    {
        $extrafeeOrderCollection = $this->extrafeeOrderCollectionFactory->create()
            ->addFilterByOrderId($orderId);

        return (bool)!$extrafeeOrderCollection->getSize();
    }
}
