<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Rma\Ui\Component\Listing\Column\Request;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\Rma\Model\Request\Resolver\OrderItem as OrderItemResolver;

/**
 * Class Products
 *
 * @package Aheadworks\Rma\Ui\Component\Listing\Column\Request
 */
class Products extends Column
{
    /**
     * @var OrderItemResolver
     */
    private $orderItemResolver;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderItemResolver $orderItemResolver
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderItemResolver $orderItemResolver,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->orderItemResolver = $orderItemResolver;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            $productNames = [];
            foreach ($item['order_items'] as &$orderItem) {
                $productNames[] = $this->orderItemResolver->getName($orderItem['item_id']);
            }
            $item[$fieldName] = implode(', ', $productNames);
        }

        return $dataSource;
    }
}
