<?php
namespace Magento\Sales\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Sales\Api\Data\CreditmemoInterface
 */
interface CreditmemoExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
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
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoInterface[]|null
     */
    public function getAwGiftcardCodes();

    /**
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoInterface[] $awGiftcardCodes
     * @return $this
     */
    public function setAwGiftcardCodes($awGiftcardCodes);
}
