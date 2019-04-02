<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Rma\Block\Adminhtml\Request\Edit\Button;

use Aheadworks\Rma\Model\Source\Request\Status;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

/**
 * Class Save
 *
 * @package Aheadworks\Rma\Block\Adminhtml\Request\Edit\Button
 */
class Save extends ButtonAbstract implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        // @todo refactoring after resolve task M2RMA-68
        $buttons = [];
        $actions = $this->getActionsConfig();
        foreach ($actions as $action) {
            $button = $this->getButton($action);
            if (empty($button)) {
                continue;
            }
            $buttons[] = $button;
        }
        uasort($buttons, [$this, 'sortButtons']);

        $primaryButton = array_shift($buttons);
        if (empty($buttons)) {
            $buttonConfig = [
                'class_name' => Container::DEFAULT_CONTROL
            ];
        } else {
            $buttonConfig = [
                'class_name' => Container::SPLIT_BUTTON,
                'options'    => $buttons
            ];
        }

        return array_merge($primaryButton, $buttonConfig);
    }

    /**
     * Retrieve button config
     *
     * @param array $action
     * @return array
     */
    private function getButton($action)
    {
        if ($this->isAvailableAction($action['action'])) {
            return [
                'label'          => __($action['label']),
                'class'          => 'save primary',
                'data_attribute' => $action['data_attribute'],
                'sort_order'     => $action['sort_order']
            ];
        }

        return [];
    }

    /**
     * Check is available action
     *
     * @param string $action
     * @return bool
     */
    protected function isAvailableAction($action)
    {
        if (null === $this->getRmaRequest()) {
            return $action == 'save';
        }

        return parent::isAvailableAction($action);
    }

    /**
     * Retrieve actions config
     *
     * @return array
     */
    private function getActionsConfig()
    {
        return [
            [
                'action' => 'approve',
                'label' => 'Approve',
                'data_attribute' => $this->prepareDataAttribute(['status_id' => Status::APPROVED]),
                'sort_order' => 80
            ],
            [
                'action' => 'package_received',
                'label' => 'Confirm Package Receiving',
                'data_attribute' => $this->prepareDataAttribute(['status_id' => Status::PACKAGE_RECEIVED]),
                'sort_order' => 80
            ],
            [
                'action' => 'issue_refund',
                'label' => 'Issue Refund',
                'data_attribute' => $this->prepareDataAttribute(['status_id' => Status::ISSUE_REFUND]),
                'sort_order' => 80
            ],
            [
                'action' => 'close',
                'label' => 'Close',
                'data_attribute' => $this->prepareDataAttribute(['status_id' => Status::CLOSED]),
                'sort_order' => 85
            ],
            [
                'action' => 'cancel',
                'label' => 'Cancel',
                'data_attribute' => $this->prepareDataAttribute(['status_id' => Status::CANCELED]),
                'sort_order' => 90
            ],
            [
                'action' => 'save',
                'label' => 'Save',
                'data_attribute' => ['mage-init' => [], 'form-role' => 'save'],
                'sort_order' => 100
            ],
        ];
    }

    /**
     * Prepare data attribute
     *
     * @param array $params
     * @return array
     */
    private function prepareDataAttribute($params)
    {
        $dataAttribute = [
            'mage-init'  => [
                'buttonAdapter' => [
                    'actions' => [
                        [
                            'targetName' => 'aw_rma_request_form.aw_rma_request_form',
                            'actionName' => 'save',
                            'params' => [
                                true,
                                array_merge($params, ['back' => 'edit'])
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $dataAttribute;
    }
}
