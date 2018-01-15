<?php
namespace Magento\Quote\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Quote\Api\Data\CartInterface
 */
interface CartExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return \Magento\Quote\Api\Data\ShippingAssignmentInterface[]|null
     */
    public function getShippingAssignments();

    /**
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface[] $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments);

    /**
     * @return float|null
     */
    public function getAwGiftcardAmount();

    /**
     * @param float $awGiftcardAmount
     * @return $this
     */
    public function setAwGiftcardAmount($awGiftcardAmount);

    /**
     * @return float|null
     */
    public function getBaseAwGiftcardAmount();

    /**
     * @param float $baseAwGiftcardAmount
     * @return $this
     */
    public function setBaseAwGiftcardAmount($baseAwGiftcardAmount);

    /**
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface[]|null
     */
    public function getAwGiftcardCodes();

    /**
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface[] $awGiftcardCodes
     * @return $this
     */
    public function setAwGiftcardCodes($awGiftcardCodes);
}
