<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\TotalsItemInterface
 */
class TotalsItemExtension extends \Magento\Framework\Api\AbstractSimpleObject implements TotalsItemExtensionInterface
{
    /**
     * @return \Amasty\Promo\Api\Data\TotalsItemImageInterface|null
     */
    public function getAmastyPromo()
    {
        return $this->_get('amasty_promo');
    }

    /**
     * @param \Amasty\Promo\Api\Data\TotalsItemImageInterface $amastyPromo
     * @return $this
     */
    public function setAmastyPromo(\Amasty\Promo\Api\Data\TotalsItemImageInterface $amastyPromo)
    {
        $this->setData('amasty_promo', $amastyPromo);
        return $this;
    }
}
