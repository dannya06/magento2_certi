<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Ui\Component\Form\Request;

use Aheadworks\Rma\Model\Request\Resolver\Order as OrderResolver;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Field;

/**
 * Class OrderInfo
 *
 * @package Aheadworks\Rma\Ui\Component\Form\Request
 */
class OrderInfo extends Field
{
    /**
     * @var OrderResolver
     */
    private $orderResolver;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UiComponentInterface[] $components
     * @param OrderResolver $orderResolver
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderResolver $orderResolver,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->orderResolver = $orderResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        parent::prepareDataSource($dataSource);
        $config = $this->getData('config');
        if (isset($dataSource['data']['order_id']) && $dataSource['data']['order_id']) {
            $orderId = $dataSource['data']['order_id'];
            $dataScope = $config['dataScope'];
            switch ($dataScope) {
                case 'order_increment_id':
                    $dataSource['data'][$dataScope . '_url'] = $this->getUrl(
                        'sales/order/view',
                        ['order_id' => $orderId]
                    );
                    $dataSource['data'][$dataScope . '_label'] =
                        '#' . $this->orderResolver->getIncrementId($orderId);
                    break;
            }
        }
        return $dataSource;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    private function getUrl($route = '', $params = [])
    {
        return $this->getContext()->getUrl($route, $params);
    }
}
