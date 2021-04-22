<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Observer\Quote;

use Amasty\Extrafee\Model\ExtrafeeQuoteRepository;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory as FeeQuoteCollectionFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Event fires when customer logged in. We need to remove all fees and recollect it
 */
class LoadCustomerQuoteObserver implements ObserverInterface
{
    /**
     * @var FeeQuoteCollectionFactory
     */
    private $feeQuoteCollectionFactory;

    /**
     * @var ExtrafeeQuoteRepository
     */
    private $feeQuoteRepository;

    public function __construct(
        FeeQuoteCollectionFactory $feeQuoteCollectionFactory,
        ExtrafeeQuoteRepository $feeQuoteRepository
    ) {
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        $this->feeQuoteRepository = $feeQuoteRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Session $checkoutSession */
        $checkoutSession = $observer->getData('checkout_session');

        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('quote_id', $checkoutSession->getQuoteId());

        foreach ($feesQuoteCollection->getItems() as $feeQuote) {
            $this->feeQuoteRepository->delete($feeQuote);
        }
    }
}
