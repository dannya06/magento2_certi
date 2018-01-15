<?php
namespace Magento\Sales\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Sales\Api\Data\InvoiceInterface
 */
interface InvoiceExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
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
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\InvoiceInterface[]|null
     */
    public function getAwGiftcardCodes();

    /**
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\InvoiceInterface[] $awGiftcardCodes
     * @return $this
     */
    public function setAwGiftcardCodes($awGiftcardCodes);
}
