<?php

namespace Icube\CustomSalable\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\ResourceConnection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{   
    protected $stockState;
    protected $productRepository;
    protected $resourceConnection;

    public function __construct(
        Context $context,
        GetProductSalableQtyInterface $stockState,
        ProductRepository $productRepository,
        ResourceConnection $resourceConnection
    ){
        $this->stockState = $stockState ;
        $this->productRepository = $productRepository;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    public function isSalable($_product)
    {
        $connection = $this->resourceConnection->getConnection();
        $query = "SELECT stock_id FROM inventory_stock_sales_channel";
        $result = $connection->fetchAll($query);
        $stockId = $result[0]['stock_id'];

        if ($_product->getTypeId() == 'configurable') {
            $_children = $_product->getTypeInstance()->getUsedProducts($_product);
            foreach ($_children as $child) {
                $qty = $this->stockState->execute($child->getSku(), $stockId);
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
                    $qty = $this->stockState->execute($child->getSku(), $stockId);
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
            $qty = $this->stockState->execute($_product->getSku(), $stockId);
            if ($qty > 0) {
                return true;
            }
        }
        
        return false;
    }
}