<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Observer\Admin\Order;

use Amasty\Extrafee\Model\TotalsInformationManagement;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Create implements ObserverInterface
{
    /** @var TotalsInformationManagement  */
    protected $totalsInformationManagement;

    /** @var Json  */
    protected $jsonDecoder;

    public function __construct(
        TotalsInformationManagement $totalsInformationManagement,
        Json $jsonDecoder
    ) {
        $this->totalsInformationManagement = $totalsInformationManagement;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $feesJson = $observer->getRequest('am_extra_fees');
        try {
            $fees = $this->jsonDecoder->unserialize($feesJson);
        } catch (\Exception $e) {
            return;
        }

        if ($fees) {
            $quote = $observer->getOrderCreateModel()->getQuote();
            foreach ($fees as $feeId => $optionIds) {
                $this->totalsInformationManagement->proceedQuoteOptions($quote, $feeId, $optionIds);
            }
        }
    }
}
