<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Observer\Quote\Model;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Payment implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($quote = $observer->getPayment()->getQuote()) {
            $quote->setAppliedAmastyFeeFlag(true);
        }
    }
}
