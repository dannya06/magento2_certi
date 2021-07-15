<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icube\Logistix\Model\Config\Source;

class Allshipping
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('All Allowed Method')],
            ['value' => 1, 'label' => __('Specific Method')]
        ];
    }
}
