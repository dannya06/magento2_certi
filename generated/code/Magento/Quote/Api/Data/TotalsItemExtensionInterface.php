<?php
namespace Magento\Quote\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Quote\Api\Data\TotalsItemInterface
 */
interface TotalsItemExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return \Amasty\Promo\Api\Data\TotalsItemImageInterface|null
     */
    public function getAmastyPromo();

    /**
     * @param \Amasty\Promo\Api\Data\TotalsItemImageInterface $amastyPromo
     * @return $this
     */
    public function setAmastyPromo(\Amasty\Promo\Api\Data\TotalsItemImageInterface $amastyPromo);
}
