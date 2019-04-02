<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Rma\Block\Adminhtml\Request\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class CreateReplacementOrder
 *
 * @package Aheadworks\Rma\Block\Adminhtml\Request\Edit\Button
 */
class CreateReplacementOrder extends ButtonAbstract implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $button = [];
        if ($this->isAvailableAction('create_replacement_order')) {
            $button = [
                'label' => __('Create a Replacement Order'),
                'class' => 'create-replacement-order',
                'on_click' => sprintf("location.href = '%s';", $this->getUrl('sales/order_create/start')),
                'sort_order' => 30
            ];
        }

        return $button;
    }
}
