<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Ui\Component\Form\Request;

use Aheadworks\Rma\Model\Request\Resolver\OrderItem as OrderItemResolver;
use Magento\CatalogInventory\Model\StockRegistryProvider;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Container;

/**
 * Class OrderItemInfo
 *
 * @package Aheadworks\Rma\Ui\Component\Form\Request
 */
class OrderItemInfo extends Container
{
    /**
     * @var OrderItemResolver
     */
    private $orderItemResolver;

    /**
     * @var StockRegistryProvider
     */
    private $stockRegistryProvider;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param ContextInterface $context
     * @param UiComponentInterface[] $components
     * @param OrderItemResolver $orderResolver
     * @param StockRegistryProvider $stockRegistryProvider
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        OrderItemResolver $orderResolver,
        StockRegistryProvider $stockRegistryProvider,
        PriceCurrencyInterface $priceCurrency,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->orderItemResolver = $orderResolver;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        parent::prepareDataSource($dataSource);
        if (isset($dataSource['data']['order_items']) && $dataSource['data']['order_items']) {
            $dataSource['data']['order_items'] = $this->prepareOrderItemsData(
                $dataSource['data']['order_items'],
                $dataSource['data']['store_id']
            );
        }
        return $dataSource;
    }

    /**
     * Prepare order items data
     *
     * @param array $orderItems
     * @param int $storeId
     * @return array
     */
    private function prepareOrderItemsData($orderItems, $storeId)
    {
        foreach ($orderItems as &$orderItem) {
            $orderItemId = $orderItem['item_id'];
            $orderItem['name_label'] = $this->orderItemResolver->getName($orderItemId);
            if ($product = $this->orderItemResolver->getItemProduct($orderItemId)) {
                $orderItem['name_url'] = $this->getUrl(
                    'catalog/product/edit',
                    ['id' => $product->getEntityId()]
                );
                $stock = $this->stockRegistryProvider->getStockItem($product->getEntityId(), $storeId);
                $orderItem['qty_in_stock'] = $stock ? $stock->getQty() : 0;
                // In the future, if not order
                //$orderItem['price'] = $this->priceCurrency->format($product->getPrice(), false);
            }
            $orderItem['price'] = $this->priceCurrency->format(
                $this->orderItemResolver->getItemWithPrice($orderItemId)->getBasePrice(),
                false
            );
            $orderItem['sku'] = $this->orderItemResolver->getSku($orderItemId);
            $orderItem['total_paid'] = $this->priceCurrency->format(
                $this->orderItemResolver->getItemPriceWithoutDiscount($orderItemId),
                false
            );
        }

        return $orderItems;
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
