<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Plugin\Order;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteManagement;

class PaymentInformationManagement
{
    /**
     * @var \Amasty\Orderattr\Model\OrderAttributesManagement
     */
    protected $_orderAttributesManager;
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    public function __construct(
        \Amasty\Orderattr\Model\OrderAttributesManagement $orderAttributesManager,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->_orderAttributesManager = $orderAttributesManager;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    public function aroundSavePaymentInformation(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($billingAddress) {
            $orderAttributes = $billingAddress->getOrderAttributes();
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $quoteId = $quoteIdMask->getQuoteId();
            if (!$quoteId) {
                $quoteId = $cartId;
            }
            $this->_orderAttributesManager->saveAttributesFromQuote($quoteId, $orderAttributes);
        }

        return $proceed($cartId, $paymentMethod, $billingAddress);
    }
}
