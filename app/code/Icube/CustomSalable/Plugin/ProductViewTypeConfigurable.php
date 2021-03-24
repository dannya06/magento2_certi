<?php

namespace Icube\CustomSalable\Plugin;

use Magento\InventorySales\Model\GetProductSalableQty;

class ProductViewTypeConfigurable
{
    protected $getProductSalableQty;

    public function __construct(
        GetProductSalableQty $getProductSalableQty
    ){
        $this->getProductSalableQty = $getProductSalableQty;
    }

    public function beforeGetAllowProducts(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject)
    {
        if (!$subject->hasData('allow_products')) {
            $products = [];
            $allProducts = $subject->getProduct()->getTypeInstance()->getUsedProducts($subject->getProduct(), null);
            foreach ($allProducts as $product) {
                $salableQty = $this->getProductSalableQty->execute($product->getSku(), 1);
                if ($product->isSaleable() && $salableQty > 0) {
                    $products[] = $product;
                }
            }
            $subject->setData('allow_products', $products);
        }
        return [];
    }
}