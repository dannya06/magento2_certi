<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;

/**
 * Class InvoiceItemsResolver
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor
 */
class InvoiceItemsResolver
{
    /**
     * @var OrderItemsResolver
     */
    private $orderItemsResolver;

    /**
     * @param OrderItemsResolver $orderItemsResolver
     */
    public function __construct(
        OrderItemsResolver $orderItemsResolver
    ) {
        $this->orderItemsResolver = $orderItemsResolver;
    }

    /**
     * @param InvoiceInterface $invoice
     * @return InvoiceItem[]
     */
    public function getItems($invoice)
    {
        $invoiceItems = [];
        /** @var OrderItemInterface $orderItems */
        $orderItems = $this->orderItemsResolver->getOrderItems($invoice->getOrderId());
        if (!empty($orderItems)) {
            /** @var InvoiceItem[] $items */
            $items = $invoice->getItems();
            foreach ($items as $item) {
                if (isset($orderItems[$item->getOrderItemId()])) {
                    /** @var OrderItemInterface $orderItem */
                    $orderItem = $orderItems[$item->getOrderItemId()];
                    $orderParentItemId = $orderItem->getParentItemId();
                    $parentItemId = null;
                    if ($orderParentItemId) {
                        $parentItem = $this->getInvoiceItemByOrderItemId($orderParentItemId, $items);
                        $parentItemId = $parentItem->getEntityId();
                    }
                    $item
                        ->setItemId($item->getEntityId())
                        ->setParentItemId($parentItemId)
                        ->setProductType($orderItem->getProductType())
                        ->setIsChildrenCalculated($orderItem->isChildrenCalculated());

                    $invoiceItems[$item->getEntityId()] = $item;
                }
            }
        }
        return $invoiceItems;
    }

    /**
     * Get invoice item by order item
     *
     * @param int $orderItemId
     * @param InvoiceItemInterface[] $invoiceItems
     * @return InvoiceItemInterface|null
     */
    private function getInvoiceItemByOrderItemId($orderItemId, $invoiceItems)
    {
        /** @var InvoiceItemInterface $invoiceItem */
        foreach ($invoiceItems as $invoiceItem) {
            if ($invoiceItem->getOrderItemId() == $orderItemId) {
                return $invoiceItem;
            }
        }
        return null;
    }
}
