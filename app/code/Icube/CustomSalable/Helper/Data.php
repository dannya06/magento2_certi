<?php

namespace Icube\CustomSalable\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\Catalog\Model\ProductRepository;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{   
    protected $stockState;
    protected $productRepository;

    public function __construct(
        Context $context,
        GetProductSalableQtyInterface $stockState,
        ProductRepository $productRepository
    ){
        $this->stockState = $stockState ;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    public function isSalable($_product)
    {
        if ($_product->getTypeId() == 'configurable') {
            $_children = $_product->getTypeInstance()->getUsedProducts($_product);
            foreach ($_children as $child) {
                $qty = $this->stockState->execute($child->getSku(), 1);
                if ($qty > 0) {
                    return true;
                }
            }

        } else if ($_product->getTypeId() == 'bundle') {
            $childrenIds = $_product->getTypeInstance()->getChildrenIds($_product->getId());
            $salable = [];
            foreach ($childrenIds as $optId => $options){
                $salable[$optId] = false;
                foreach ($options as $childId) {
                    $child = $this->productRepository->getById($childId);
                    $qty = $this->stockState->execute($child->getSku(), 1);
                    if ($qty > 0) {
                        $salable[$optId] = true;
                    }
                }
            }
            if (in_array(false, $salable)) {
                return false;
            } else {
                return true;
            }

        } else {
            $qty = $this->stockState->execute($_product->getSku(), 1);
            if ($qty > 0) {
                return true;
            }
        }
        
        return false;
    }
}