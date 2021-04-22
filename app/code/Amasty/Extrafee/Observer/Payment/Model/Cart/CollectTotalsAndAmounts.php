<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Observer\Payment\Model\Cart;

use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory as FeeQuoteCollectionFactory;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Paypal\Model\Cart;

class CollectTotalsAndAmounts implements ObserverInterface
{
    /**
     * @var FeeQuoteCollectionFactory
     */
    private $feeQuoteCollectionFactory;

    public function __construct(FeeQuoteCollectionFactory $feeQuoteCollectionFactory)
    {
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getCart();
        $id = $cart->getSalesModel()->getDataUsingMethod('entity_id');

        if (!$id) {
            $id = $cart->getSalesModel()->getDataUsingMethod('quote_id');
        }
        
        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('option_id', ['neq' => '0'])
            ->addFieldToFilter('quote_id', $id);

        $labels = [];
        $baseFeeAmount = 0;

        foreach ($feesQuoteCollection as $feeOption) {
            $baseFeeAmount += $feeOption->getBaseFeeAmount();
            $labels[] = $feeOption->getLabel();
        }

        $cart->addCustomItem(implode(', ', $labels), 1, $baseFeeAmount);
    }
}
