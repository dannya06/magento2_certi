<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

class GuestTotalsInformationManagement implements \Amasty\Extrafee\Api\GuestTotalsInformationManagementInterface
{
    /** @var \Magento\Quote\Model\QuoteIdMaskFactory */
    protected $quoteIdMaskFactory;

    /** @var \Amasty\Extrafee\Api\TotalsInformationManagementInterface */
    protected $totalsInformationManagement;

    /**
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Amasty\Extrafee\Api\TotalsInformationManagementInterface $totalsInformationManagement
     */
    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Amasty\Extrafee\Api\TotalsInformationManagementInterface $totalsInformationManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->totalsInformationManagement = $totalsInformationManagement;
    }

    public function calculate(
        $cartId,
        \Amasty\Extrafee\Api\Data\TotalsInformationInterface $information,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        /** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->totalsInformationManagement->calculate(
            $quoteIdMask->getQuoteId(),
            $information,
            $addressInformation
        );
    }
}
