<?php

namespace Icube\CatalogSorting\Model;

class Config extends \Magento\Catalog\Model\Config
{
    public function getAttributeUsedForSortByArray()
    {
       $options = [
              'newest' => __('Newest'),
              'position' => __('Position'),
       				'bestseller' => __('Best Seller'),
       				'most_viewed' => __('Most Viewed'),
   				];
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }

       return $options;
    }
}