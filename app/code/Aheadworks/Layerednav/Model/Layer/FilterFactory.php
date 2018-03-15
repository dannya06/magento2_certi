<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Filter;
use Aheadworks\Layerednav\Model\Layer\Filter\Custom as CustomFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderPool;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Class FilterFactory
 * @package Aheadworks\Layerednav\Model\Layer
 */
class FilterFactory
{
    /**
     * @var string[]
     */
    private $filterTypes = [
        FilterInterface::CATEGORY_FILTER => \Aheadworks\Layerednav\Model\Layer\Filter\Category::class,
        FilterInterface::ATTRIBUTE_FILTER => \Aheadworks\Layerednav\Model\Layer\Filter\Attribute::class,
        FilterInterface::PRICE_FILTER => \Aheadworks\Layerednav\Model\Layer\Filter\Price::class,
        FilterInterface::DECIMAL_FILTER => \Aheadworks\Layerednav\Model\Layer\Filter\Decimal::class,
        FilterInterface::STOCK_FILTER => 'Aheadworks\Layerednav\Model\Layer\Filter\Custom\Stock',
        FilterInterface::SALES_FILTER => 'Aheadworks\Layerednav\Model\Layer\Filter\Custom\Sales',
        FilterInterface::NEW_FILTER => 'Aheadworks\Layerednav\Model\Layer\Filter\Custom\NewProduct'
    ];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DataBuilderPool
     */
    private $dataBuilderPool;

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     * @param DataBuilderPool $dataBuilderPool
     * @param FilterRepositoryInterface $filterRepository
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config,
        DataBuilderPool $dataBuilderPool,
        FilterRepositoryInterface $filterRepository
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->dataBuilderPool = $dataBuilderPool;
        $this->filterRepository = $filterRepository;
    }

    /**
     * Create layer filter
     *
     * @param FilterInterface $filterObject
     * @param Layer $layer
     * @param Attribute|null $attribute
     * @return AbstractFilter|null
     * @throws \Exception
     */
    public function create($filterObject, $layer, $attribute = null)
    {
        if (in_array($filterObject->getType(), FilterInterface::CUSTOM_FILTER_TYPES)) {
            /** @var CustomFilter $filter */
            $filter = $this->createCustomFilter($filterObject, $layer);
            $filter->setName($filterObject->getStorefrontTitle());
            $filter->setSeoFriendlyValue($filterObject->getType());
            if ($filterObject->getType() == FilterInterface::CATEGORY_FILTER) {
                /** @var FilterInterface|Filter $categoryFilterObject */
                $categoryFilterObject = $this->filterRepository->get($filterObject->getId());
                /** @var FilterCategoryInterface $categoruFilterData */
                $categoryFilterData = $categoryFilterObject->getCategoryFilterData();
                if ($categoryFilterData) {
                    $filter->setStorefrontListStyle($categoryFilterData->getStorefrontListStyle());
                }
            }
        } else {
            $filter = $this->createAttributeFilter($filterObject, $layer, $attribute);
        }

        $storeFrontDisplayState = $filterObject->getStorefrontDisplayState();
        if (!$storeFrontDisplayState) {
            $storeFrontDisplayState = $this->config->getFilterDisplayState();
        }
        $filter->setStorefrontDisplayState($storeFrontDisplayState);

        return $filter;
    }

    /**
     * Create custom filter
     *
     * @param FilterInterface $filterEntity
     * @param Layer $layer
     * @return AbstractFilter
     * @throws \Exception
     */
    private function createCustomFilter($filterEntity, Layer $layer)
    {
        return $this->objectManager->create(
            $this->filterTypes[$filterEntity->getType()],
            [
                'layer' => $layer,
                'itemDataBuilder' => $this->dataBuilderPool->getDataBuilder(
                    $filterEntity->getStorefrontSortOrder()
                )
            ]
        );
    }

    /**
     * Create attribute filter
     *
     * @param FilterInterface $filterEntity
     * @param Layer $layer
     * @param Attribute $attribute
     * @return AbstractFilter
     * @throws \Exception
     */
    private function createAttributeFilter($filterEntity, Layer $layer, $attribute)
    {
        if (null == $attribute) {
            throw new \Exception(__('No attribute specified!'));
        }

        return $this->objectManager->create(
            $this->filterTypes[$filterEntity->getType()],
            [
                'data' => ['attribute_model' => $attribute],
                'layer' => $layer,
                'itemDataBuilder' => $this->dataBuilderPool->getDataBuilder(
                    $filterEntity->getStorefrontSortOrder()
                )
            ]
        );
    }
}
