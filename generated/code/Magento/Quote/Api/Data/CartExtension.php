<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\CartInterface
 */
class CartExtension extends \Magento\Framework\Api\AbstractSimpleObject implements CartExtensionInterface
{
    /**
     * @return \Magento\Quote\Api\Data\ShippingAssignmentInterface[]|null
     */
    public function getShippingAssignments()
    {
        return $this->_get('shipping_assignments');
    }

    /**
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface[] $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments)
    {
        $this->setData('shipping_assignments', $shippingAssignments);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwGiftcardAmount()
    {
        return $this->_get('aw_giftcard_amount');
    }

    /**
     * @param float $awGiftcardAmount
     * @return $this
     */
    public function setAwGiftcardAmount($awGiftcardAmount)
    {
        $this->setData('aw_giftcard_amount', $awGiftcardAmount);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwGiftcardAmount()
    {
        return $this->_get('base_aw_giftcard_amount');
    }

    /**
     * @param float $baseAwGiftcardAmount
     * @return $this
     */
    public function setBaseAwGiftcardAmount($baseAwGiftcardAmount)
    {
        $this->setData('base_aw_giftcard_amount', $baseAwGiftcardAmount);
        return $this;
    }

    /**
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface[]|null
     */
    public function getAwGiftcardCodes()
    {
        return $this->_get('aw_giftcard_codes');
    }

    /**
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface[] $awGiftcardCodes
     * @return $this
     */
    public function setAwGiftcardCodes($awGiftcardCodes)
    {
        $this->setData('aw_giftcard_codes', $awGiftcardCodes);
        return $this;
    }
}
