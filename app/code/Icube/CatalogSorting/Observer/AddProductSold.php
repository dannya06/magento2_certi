<?php

namespace Icube\CatalogSorting\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddProductSold implements ObserverInterface
{
    protected $order;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\Order $order
    ) {
        $this->_productFactory = $productFactory;
        $this->_order = $order;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrderIds();
        $order = $this->_order->load($orderId);

        // get Order All Item
        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            $product = $this->_productFactory->create()->load($item->getData('product_id'));
            $product->setData('icube_sold', $product->getData('icube_sold') + $item->getData('qty_ordered'))->save();
        }
    }
}
