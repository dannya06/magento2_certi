<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Api;

use Magento\Quote\Api\Data\AddressInterface;

interface FeeEstimationInterface
{
    /**
     * Estimate shipping by quote and return list of available shipping methods
     * @param mixed $cartId
     * @param AddressInterface $address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods
     */
    public function estimateByExtendedAddress($cartId, AddressInterface $address);
}
