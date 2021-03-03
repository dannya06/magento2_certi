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

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Item;

/**
 * Class CreditmemoItemsResolver
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor
 */
class CreditmemoItemsResolver
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
     * @param CreditmemoInterface $creditmemo
     * @return CreditmemoItem[]
     */
    public function getItems($creditmemo)
    {
        $creditmemoItems = [];
        /** @var OrderItemInterface[] $orderItems */
        $orderItems = $this->orderItemsResolver->getOrderItems($creditmemo->getOrderId());
        if (!empty($orderItems)) {
            /** @var CreditmemoItem[] $items */
            $items = $creditmemo->getItems();
            foreach ($items as $item) {
                if ($this->isNeedToProcessCreditmemoItem($item, $orderItems)) {
                    /** @var Item $orderItem */
                    $orderItem = $orderItems[$item->getOrderItemId()];
                    $orderParentItemId = $orderItem->getParentItemId();
                    $parentItemId = null;
                    if ($orderParentItemId) {
                        $parentItem = $this->getCreditmemoItemByOrderItemId($orderParentItemId, $items);
                        $parentItemId = is_object($parentItem) ? $parentItem->getEntityId() : null;
                    }
                    $item
                        ->setItemId($item->getEntityId())
                        ->setParentItemId($parentItemId)
                        ->setProductType($orderItem->getProductType())
                        ->setIsChildrenCalculated($orderItem->isChildrenCalculated());

                    $creditmemoItems[$item->getEntityId()] = $item;
                }
            }
        }
        return $creditmemoItems;
    }

    /**
     * Check if need to process specified creditmemo item
     *
     * @param CreditmemoItem $creditmemoItem
     * @param OrderItemInterface[] $orderItems
     * @return bool
     */
    private function isNeedToProcessCreditmemoItem($creditmemoItem, $orderItems)
    {
        return isset($orderItems[$creditmemoItem->getOrderItemId()])
            && $creditmemoItem->getQty() > 0;
    }

    /**
     * Get creditmemo item by order item
     *
     * @param int $orderItemId
     * @param CreditmemoItemInterface[] $creditmemoItems
     * @return CreditmemoItemInterface|null
     */
    private function getCreditmemoItemByOrderItemId($orderItemId, $creditmemoItems)
    {
        /** @var CreditmemoItemInterface $creditmemoItem */
        foreach ($creditmemoItems as $creditmemoItem) {
            if ($creditmemoItem->getOrderItemId() == $orderItemId) {
                return $creditmemoItem;
            }
        }
        return null;
    }
}
