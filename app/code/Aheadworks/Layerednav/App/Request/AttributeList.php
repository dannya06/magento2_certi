<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\CacheInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;

/**
 * Class AttributeList
 * @package Aheadworks\Layerednav\App\Request
 */
class AttributeList
{
    /**#@+
     * Attribute list types
     */
    const LIST_TYPE_DEFAULT = 'default';
    const LIST_TYPE_DECIMAL = 'decimal';
    /**#@-*/

    /**
     * @var string
     */
    const ATTRIBUTES_CACHE_KEY = 'aw_ln_attributes';

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CacheInterface $cache
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CacheInterface $cache
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cache = $cache;
    }

    /**
     * Get attribute list keyed by attribute code
     *
     * @param string $listType
     * @return array
     */
    public function getAttributesKeyedByCode($listType = self::LIST_TYPE_DEFAULT)
    {
        $result = [];
        foreach ($this->getAttributes($listType) as $attribute) {
            $result[$attribute['code']] = $attribute;
        }
        return $result;
    }

    /**
     * Get attribute codes
     *
     * @param string $listType
     * @return array
     */
    public function getAttributeCodes($listType = self::LIST_TYPE_DEFAULT)
    {
        $result = [];
        foreach ($this->getAttributes($listType) as $attribute) {
            $result[] = $attribute['code'];
        }
        return $result;
    }

    /**
     * Get attributes from cache
     *
     * @param string $listType
     * @return array
     */
    private function getAttributes($listType = self::LIST_TYPE_DEFAULT)
    {
        if (!isset($this->attributes[$listType])) {
            $attributes = $this->cache->load(self::ATTRIBUTES_CACHE_KEY);
            $this->attributes = $attributes
                ? unserialize($attributes)
                : [];

            if (!isset($this->attributes[$listType])) {
                $this->attributes[$listType] = $this->loadAttributes($listType);
                $this->cache->save(serialize($this->attributes), self::ATTRIBUTES_CACHE_KEY, [], null);
            }
        }
        return $this->attributes[$listType];
    }

    /**
     * Load attributes from DB
     *
     * @param string $listType
     * @return array
     */
    private function loadAttributes($listType)
    {
        $this->searchCriteriaBuilder
            ->addFilter(ProductAttributeInterface::IS_VISIBLE, true)
            ->addFilter(ProductAttributeInterface::IS_FILTERABLE, true);
        if ($listType == self::LIST_TYPE_DEFAULT) {
            $this->searchCriteriaBuilder
                ->addFilter(ProductAttributeInterface::ATTRIBUTE_CODE, 'price', 'neq')
                ->addFilter(ProductAttributeInterface::BACKEND_TYPE, 'decimal', 'neq');
        } elseif ($listType == self::LIST_TYPE_DECIMAL) {
            $this->searchCriteriaBuilder
                ->addFilter(ProductAttributeInterface::BACKEND_TYPE, 'decimal', 'eq');
        }
        $attributes = $this->productAttributeRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        $attributesArray = [];
        /** @var ProductAttributeInterface|EavAttribute $attribute */
        foreach ($attributes as $attribute) {
            $attributesArray[] = [
                'code' => $attribute->getAttributeCode(),
                'options' => $attribute->getOptions(),
                'select_options' => $attribute->getFrontend()->getSelectOptions()
            ];
        }

        return $attributesArray;
    }
}
