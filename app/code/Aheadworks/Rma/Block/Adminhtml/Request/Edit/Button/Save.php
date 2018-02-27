<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Block\Adminhtml\Request\Edit\Button;

use Aheadworks\Rma\Api\RequestRepositoryInterface;
use Aheadworks\Rma\Model\Source\Request\Status;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Aheadworks\Rma\Model\Request\Resolver\Status as StatusResolver;
use Magento\Backend\Block\Widget\Context;
use Magento\Ui\Component\Control\Container;

/**
 * Class Save
 *
 * @package Aheadworks\Rma\Block\Adminhtml\Request\Edit\Button
 */
class Save implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var RequestRepositoryInterface
     */
    private $requestRepository;

    /**
     * @var StatusResolver
     */
    private $statusResolver;

    /**
     * @param Context $context
     * @param RequestRepositoryInterface $requestRepository
     * @param StatusResolver $statusResolver
     */
    public function __construct(
        Context $context,
        RequestRepositoryInterface $requestRepository,
        StatusResolver $statusResolver
    ) {
        $this->context = $context;
        $this->requestRepository = $requestRepository;
        $this->statusResolver = $statusResolver;
    }

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
        if (!$this->statusResolver->isAvailableActionForStatus($action['action'], $this->getRmaRequest(), true)) {
            return [];
        }

        return [
            'label'          => __($action['label']),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init'  => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'aw_rma_request_form.aw_rma_request_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    array_merge($action['params'], ['back' => 'edit'])
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'sort_order'     => $action['sort_order']
        ];
    }

    /**
     * Sort buttons by sort order
     *
     * @param array $itemA
     * @param array $itemB
     * @return int
     */
    public function sortButtons(array $itemA, array $itemB)
    {
        $sortOrderA = isset($itemA['sort_order']) ? intval($itemA['sort_order']) : 0;
        $sortOrderB = isset($itemB['sort_order']) ? intval($itemB['sort_order']) : 0;

        return $sortOrderA - $sortOrderB;
    }

    /**
     * Retrieve RMA request
     *
     * @return \Aheadworks\Rma\Api\Data\RequestInterface
     */
    private function getRmaRequest()
    {
        return $this->requestRepository->get($this->context->getRequest()->getParam('id'));
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
                'params' => ['status_id' => Status::APPROVED],
                'sort_order' => 80
            ],
            [
                'action' => 'package_received',
                'label' => 'Confirm Package Receiving',
                'params' => ['status_id' => Status::PACKAGE_RECEIVED],
                'sort_order' => 80
            ],
            [
                'action' => 'issue_refund',
                'label' => 'Issue Refund',
                'params' => ['status_id' => Status::ISSUE_REFUND],
                'sort_order' => 80
            ],
            [
                'action' => 'close',
                'label' => 'Close',
                'params' => ['status_id' => Status::CLOSED],
                'sort_order' => 85
            ],
            [
                'action' => 'cancel',
                'label' => 'Cancel',
                'params' => ['status_id' => Status::CANCELED],
                'sort_order' => 90
            ],
            [
                'action' => 'update',
                'label' => 'Save',
                'params' => ['back' => 'edit'],
                'sort_order' => 100
            ],
        ];
    }
}
