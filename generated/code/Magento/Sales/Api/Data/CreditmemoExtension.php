<?php
namespace Magento\Sales\Api\Data;

/**
 * Extension class for @see \Magento\Sales\Api\Data\CreditmemoInterface
 */
class CreditmemoExtension extends \Magento\Framework\Api\AbstractSimpleObject implements CreditmemoExtensionInterface
{
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
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoInterface[]|null
     */
    public function getAwGiftcardCodes()
    {
        return $this->_get('aw_giftcard_codes');
    }

    /**
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoInterface[] $awGiftcardCodes
     * @return $this
     */
    public function setAwGiftcardCodes($awGiftcardCodes)
    {
        $this->setData('aw_giftcard_codes', $awGiftcardCodes);
        return $this;
    }
}
