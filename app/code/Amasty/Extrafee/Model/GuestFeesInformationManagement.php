<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\GuestFeesInformationManagementInterface;
use Amasty\Extrafee\Api\FeesInformationManagementInterface;
use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestFeesInformationManagement implements GuestFeesInformationManagementInterface
{
    /** @var QuoteIdMaskFactory */
    protected $quoteIdMaskFactory;

    /** @var  FeesInformationManagementInterface */
    protected $feesInformationManagement;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        FeesInformationManagementInterface $feesInformationManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->feesInformationManagement = $feesInformationManagement;
    }

    /**
     * @param string $cartId
     * @param TotalsInformationInterface $addressInformation
     *
     * @return \Amasty\Extrafee\Api\Data\FeesManagerInterface
     */
    public function collect(
        $cartId,
        TotalsInformationInterface $addressInformation
    ) {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->feesInformationManagement->collect(
            $quoteIdMask->getQuoteId(),
            $addressInformation
        );
    }
}
