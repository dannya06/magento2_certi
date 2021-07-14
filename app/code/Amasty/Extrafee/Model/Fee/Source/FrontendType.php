<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\Fee\Source;

use Amasty\Extrafee\Model\Fee;
use Magento\Framework\Data\OptionSourceInterface;

class FrontendType implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Checkbox'),
                'value' => Fee::FRONTEND_TYPE_CHECKBOX
            ],
            [
                'label' => __('Dropdown'),
                'value' => Fee::FRONTEND_TYPE_DROPDOWN
            ],
            [
                'label' => __('Radio Button'),
                'value' => Fee::FRONTEND_TYPE_RADIO
            ]
        ];
    }
}
