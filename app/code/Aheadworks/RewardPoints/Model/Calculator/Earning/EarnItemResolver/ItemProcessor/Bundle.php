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
namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessor;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterfaceFactory;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessorInterface;

/**
 * Class Bundle
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessor
 */
class Bundle implements ItemProcessorInterface
{
    /**
     * @var EarnItemInterfaceFactory
     */
    private $earnItemFactory;

    /**
     * @param EarnItemInterfaceFactory $earnItemFactory
     */
    public function __construct(
        EarnItemInterfaceFactory $earnItemFactory
    ) {
        $this->earnItemFactory = $earnItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getEarnItem($groupedItems, $beforeTax = true)
    {
        /** @var EarnItemInterface $earnItem */
        $earnItem = $this->earnItemFactory->create();

        /** @var ItemInterface $parenItem */
        $parenItem = $this->getParentItem($groupedItems);
        /** @var ItemInterface[] $childItems */
        $childItems = $this->getChildItems($groupedItems);
        if ($parenItem && !empty($childItems)) {
            if ($parenItem->getIsChildrenCalculated()) {
                $baseSubtotal = 0;
                foreach ($childItems as $childItem) {
                    $baseSubtotal += $beforeTax ? $childItem->getBaseRowTotal() : $childItem->getBaseRowTotalInclTax();
                    $discount = $childItem->getBaseDiscountAmount() + $childItem->getBaseAwRewardPointsAmount();
                    $baseSubtotal -= $discount;
                }

                $earnItem
                    ->setProductId($parenItem->getProductId())
                    ->setBaseAmount($baseSubtotal)
                    ->setQty($parenItem->getQty());
            } else {
                $baseSubtotal = $beforeTax ? $parenItem->getBaseRowTotal() : $parenItem->getBaseRowTotalInclTax();
                $discount = $parenItem->getBaseDiscountAmount() + $parenItem->getBaseAwRewardPointsAmount();
                $baseSubtotal -= $discount;

                $earnItem
                    ->setProductId($parenItem->getProductId())
                    ->setBaseAmount($baseSubtotal)
                    ->setQty($parenItem->getQty());
            }
        } else {
            $earnItem
                ->setProductId(null)
                ->setBaseAmount(0)
                ->setQty(0);
        }

        return $earnItem;
    }

    /**
     * Get parent item
     *
     * @param ItemInterface[] $items
     * @return ItemInterface|null
     */
    private function getParentItem($items)
    {
        foreach ($items as $item) {
            if ($item->getParentItem() == null) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Get child items
     *
     * @param ItemInterface[] $items
     * @return ItemInterface[]
     */
    private function getChildItems($items)
    {
        $childItems = [];
        foreach ($items as $item) {
            if ($item->getParentItem() != null) {
                $childItems[] =  $item;
            }
        }
        return $childItems;
    }
}
