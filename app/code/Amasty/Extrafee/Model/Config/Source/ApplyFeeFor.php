<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ApplyFeeFor implements OptionSourceInterface
{
    const FOR_CART = 0;
    const PER_PRODUCT = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::FOR_CART,
                'label' => __('Whole Cart')
            ],
            [
                'value' => self::PER_PRODUCT,
                'label' => __('Each Product in the Cart')
            ]
        ];
    }
}
