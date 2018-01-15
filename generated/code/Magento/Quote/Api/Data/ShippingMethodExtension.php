<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\ShippingMethodInterface
 */
class ShippingMethodExtension extends \Magento\Framework\Api\AbstractSimpleObject implements ShippingMethodExtensionInterface
{
    /**
     * @return string|null
     */
    public function getEstimation()
    {
        return $this->_get('estimation');
    }

    /**
     * @param string $estimation
     * @return $this
     */
    public function setEstimation($estimation)
    {
        $this->setData('estimation', $estimation);
        return $this;
    }
}
