<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Observer\Sales\Model\Order;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Api\ExtrafeeOrderRepositoryInterface;
use Amasty\Extrafee\Api\ExtrafeeInvoiceRepositoryInterface;
use Amasty\Extrafee\Model\ExtrafeeInvoiceFactory;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\CollectionFactory as ExtrafeeOrderCollectionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * save extra fee data when submit invoice, 'sales_order_invoice_register' event
 */
class Invoice implements ObserverInterface
{
    /**
     * @var ExtrafeeInvoiceFactory
     */
    private $extrafeeInvoiceFactory;

    /**
     * @var ExtrafeeOrderRepositoryInterface
     */
    private $extrafeeOrderRepository;

    /**
     * @var ExtrafeeInvoiceRepositoryInterface
     */
    private $extrafeeInvoiceRepository;

    /**
     * @var ExtrafeeOrderCollectionFactory
     */
    private $extrafeeOrderCollectionFactory;

    public function __construct(
        ExtrafeeInvoiceFactory $extrafeeInvoiceFactory,
        ExtrafeeOrderRepositoryInterface $extrafeeOrderRepository,
        ExtrafeeInvoiceRepositoryInterface $extrafeeInvoiceRepository,
        ExtrafeeOrderCollectionFactory $extrafeeOrderCollectionFactory
    ) {
        $this->extrafeeInvoiceFactory = $extrafeeInvoiceFactory;
        $this->extrafeeOrderRepository = $extrafeeOrderRepository;
        $this->extrafeeInvoiceRepository = $extrafeeInvoiceRepository;
        $this->extrafeeOrderCollectionFactory = $extrafeeOrderCollectionFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $orderId = $invoice->getOrderId();
        $extrafeeOrderCollection = $this->extrafeeOrderCollectionFactory->create()
            ->addFieldToFilter(ExtrafeeOrderInterface::ORDER_ID, $orderId);

        /** @var \Amasty\Extrafee\Model\ExtrafeeOrder $extrafeeOrder */
        foreach ($extrafeeOrderCollection->getItems() as $extrafeeOrder) {
            /** for invoice only first time */
            if ($extrafeeOrder->getBaseTotalAmountInvoiced() == 0) {
                $extrafeeOrder->setBaseTotalAmountInvoiced((float)$extrafeeOrder->getBaseTotalAmount());
                $extrafeeOrder->setTotalAmountInvoiced((float)$extrafeeOrder->getTotalAmount());
                $extrafeeOrder->setBaseTaxAmountInvoiced((float)$extrafeeOrder->getBaseTaxAmount());
                $extrafeeOrder->setTaxAmountInvoiced((float)$extrafeeOrder->getTaxAmount());

                $this->extrafeeOrderRepository->save($extrafeeOrder);

                $this->createExtrafeeInvoice($extrafeeOrder, $orderId, $invoice->getId());
            }
        }
    }

    /**
     * @param ExtrafeeOrderInterface $extrafeeOrder
     * @param int $orderId
     * @param int $invoiceId
     */
    private function createExtrafeeInvoice(ExtrafeeOrderInterface $extrafeeOrder, $orderId, $invoiceId)
    {
        $extrafeeInvoice = $this->extrafeeInvoiceFactory->create();

        $extrafeeInvoice->setOrderId((int)$orderId);
        $extrafeeInvoice->setInvoiceId((int)$invoiceId);
        $extrafeeInvoice->setFeeId((int)$extrafeeOrder->getFeeId());
        $extrafeeInvoice->setOptionId((int)$extrafeeOrder->getOptionId());
        $extrafeeInvoice->setBaseTotalAmount((float)$extrafeeOrder->getBaseTotalAmount());
        $extrafeeInvoice->setTotalAmount((float)$extrafeeOrder->getTotalAmount());
        $extrafeeInvoice->setBaseTaxAmount((float)$extrafeeOrder->getBaseTaxAmount());
        $extrafeeInvoice->setTaxAmount((float)$extrafeeOrder->getTaxAmount());
        $extrafeeInvoice->setFeeLabel($extrafeeOrder->getLabel());
        $extrafeeInvoice->setFeeOptionLabel($extrafeeOrder->getOptionLabel());

        $this->extrafeeInvoiceRepository->save($extrafeeInvoice);
    }
}
