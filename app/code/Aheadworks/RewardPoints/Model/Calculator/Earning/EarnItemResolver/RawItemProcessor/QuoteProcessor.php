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

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Class QuoteProcessor
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor
 */
class QuoteProcessor
{
    /**
     * @var ItemGroupConverterInterface
     */
    private $itemGroupConverter;

    /**
     * @param ItemGroupConverterInterface $itemGroupConverter
     */
    public function __construct(
        ItemGroupConverterInterface $itemGroupConverter
    ) {
        $this->itemGroupConverter = $itemGroupConverter;
    }

    /**
     * @param Quote $quote
     * @return array [ItemInterface[], ...]
     */
    public function getItemGroups($quote)
    {
        /** @var QuoteItem[] $quoteItems */
        $quoteItems = $quote->getAllItems();
        $quoteItemGroups = $this->getQuoteItemsGrouped($quoteItems);
        $itemGroups = $this->itemGroupConverter->convert($quoteItemGroups);

        return $itemGroups;
    }

    /**
     * Get quote items grouped
     *
     * @param QuoteItem[] $quoteItems
     * @return array
     */
    private function getQuoteItemsGrouped($quoteItems)
    {
        $quoteGroups = [];
        /** @var QuoteItem $quoteItem */
        foreach ($quoteItems as $quoteItem) {
            $parentItemId = $quoteItem->getParentItemId();
            if ($parentItemId == null) {
                $parentItemId = $quoteItem->getItemId();
            }
            $quoteItem->setIsChildrenCalculated($quoteItem->isChildrenCalculated());
            $quoteGroups[$parentItemId][$quoteItem->getItemId()] = $quoteItem;
        }
        return $quoteGroups;
    }
}
