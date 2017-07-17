<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Model;

use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface as GiftcardQuoteInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Quote\CollectionFactory as GiftcardQuoteCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;

/**
 * Class QuotePlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Model
 */
class QuotePlugin
{
    /**
     * @var GiftcardQuoteCollectionFactory
     */
    private $giftcardQuoteCollectionFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param GiftcardQuoteCollectionFactory $giftcardQuoteCollectionFactory
     * @param EntityManager $entityManager
     */
    public function __construct(
        GiftcardQuoteCollectionFactory $giftcardQuoteCollectionFactory,
        EntityManager $entityManager
    ) {
        $this->giftcardQuoteCollectionFactory = $giftcardQuoteCollectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * Replace quote_id in Gift Card quote table after merge quote
     *
     * @param Quote $subject
     * @param \Closure $proceed
     * @param Quote $quote
     * @return Quote
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundMerge(Quote $subject, \Closure $proceed, Quote $quote)
    {
        /** @var Quote $quoteAfterMerge */
        $quoteAfterMerge = $proceed($quote);

        $giftcardQuoteItems = $this->giftcardQuoteCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quote->getId())
            ->load()
            ->getItems();

        if (!$giftcardQuoteItems) {
            return $quoteAfterMerge;
        }

        /** @var GiftcardQuoteInterface $giftcardQuoteItem */
        foreach ($giftcardQuoteItems as $giftcardQuoteItem) {
            $giftcardQuoteItem->setQuoteId($quoteAfterMerge->getId());
            $this->entityManager->save($giftcardQuoteItem);
        }

        return $quoteAfterMerge;
    }
}
