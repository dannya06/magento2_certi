<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Rma\Block\Adminhtml\Request\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Rma\Api\RequestRepositoryInterface;
use Aheadworks\Rma\Model\Request\Resolver\Status as StatusResolver;

/**
 * Class CreateCreditMemo
 *
 * @package Aheadworks\Rma\Block\Adminhtml\Request\Edit\Button
 */
class CreateCreditMemo extends ButtonAbstract implements ButtonProviderInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param Context $context
     * @param RequestRepositoryInterface $requestRepository
     * @param StatusResolver $statusResolver
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        RequestRepositoryInterface $requestRepository,
        StatusResolver $statusResolver,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context, $requestRepository, $statusResolver);
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $button = [];
        $orderId = $this->getOrderId();
        if ($this->isAvailableAction('create_credit_memo') && $this->canCreditMemo($orderId)) {
            $button = [
                'label' => __('Create a Credit Memo'),
                'class' => 'create-credit-memo',
                'on_click' => sprintf(
                    "location.href = '%s';",
                    $this->getUrl('sales/order_creditmemo/new', ['order_id' => $orderId])
                ),
                'sort_order' => 30
            ];
        }

        return $button;
    }

    /**
     * Retrieve order id
     *
     * @return int
     */
    private function getOrderId()
    {
        $request = $this->getRmaRequest();

        return $request ? $request->getOrderId() : 0;
    }

    /**
     * Check is it possible to credit memo
     *
     * @param int $orderId
     * @return bool
     */
    private function canCreditMemo($orderId)
    {
        if ($orderId) {
            $order = $this->orderRepository->get($orderId);
            return $order->canCreditmemo();
        }
        return false;
    }
}
