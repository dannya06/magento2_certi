<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Plugin\Order;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteManagement;

class ShippingInformationManagement
{
    /**
     * @var \Amasty\Orderattr\Model\OrderAttributesManagement
     */
    protected $_orderAttributesManager;

    public function __construct(
        \Amasty\Orderattr\Model\OrderAttributesManagement $orderAttributesManager
    ) {
        $this->_orderAttributesManager = $orderAttributesManager;
    }

    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Model\ShippingInformation $addressInformation
    ) {
        if ($addressInformation && $addressInformation->getShippingAddress()) {
            $orderAttributes = $addressInformation->getShippingAddress()->getOrderAttributes();
            $this->_orderAttributesManager->saveAttributesFromQuote($cartId, $orderAttributes);
        }

        return $proceed($cartId, $addressInformation);
    }
}
