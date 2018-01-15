<?php
namespace Magento\Quote\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Quote\Api\Data\ShippingMethodInterface
 */
interface ShippingMethodExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return string|null
     */
    public function getEstimation();

    /**
     * @param string $estimation
     * @return $this
     */
    public function setEstimation($estimation);
}
