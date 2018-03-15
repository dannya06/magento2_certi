<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\FilterFactory as LayerFilterFactory;
use Aheadworks\Layerednav\Model\Filter\CategoryValidator as FilterCategoryValidator;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Class FilterListAbstract
 * @package Aheadworks\Layerednav\Model\Layer
 */
abstract class FilterListAbstract
{
    /**
     * @var LayerFilterFactory
     */
    private $layerFilterFactory;

    /**
     * @var FilterableAttributeListInterface
     */
    private $filterableAttributes;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AbstractFilter[]
     */
    private $filters;

    /**
     * @var FilterCategoryValidator
     */
    private $filterCategoryValidator;

    /**
     * @var FilterRepositoryInterface
     */
    protected $filterRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var array
     */
    private $customFilters;

    /**
     * @param FilterFactory $layerFilterFactory
     * @param FilterableAttributeListInterface $filterableAttributes
     * @param Config $config
     * @param FilterCategoryValidator $filterCategoryValidator
     * @param FilterRepositoryInterface $filterRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        LayerFilterFactory $layerFilterFactory,
        FilterableAttributeListInterface $filterableAttributes,
        Config $config,
        FilterCategoryValidator $filterCategoryValidator,
        FilterRepositoryInterface $filterRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->layerFilterFactory = $layerFilterFactory;
        $this->filterableAttributes = $filterableAttributes;
        $this->config = $config;
        $this->filterCategoryValidator = $filterCategoryValidator;
        $this->filterRepository = $filterRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Get filters
     *
     * @param Layer $layer
     * @return AbstractFilter[]
     * @throws \Exception
     */
    public function getFilters(Layer $layer)
    {
        if (!$this->filters) {
            $filters = [];

            /** @var FilterInterface[] $filterObjects */
            $filterObjects = $this->getFilterDataObjects($layer);
            $filterableAttributesList = $this->filterableAttributes->getList();
            if (is_array($filterableAttributesList)) {
                $filterableAttributes = $filterableAttributesList;
            } else {
                $filterableAttributes = $filterableAttributesList->getItems();
            }

            foreach ($filterObjects as $filterObject) {
                /** @var Category $currentCategory */
                $currentCategory = $layer->getCurrentCategory();
                if ($this->filterCategoryValidator->validate($filterObject, $currentCategory)) {
                    if ($this->isCustomFilter($filterObject->getType())) {
                        if ($this->isAvailableCustomFilter($filterObject->getType())) {
                            $filters[] = $this->layerFilterFactory->create($filterObject, $layer);
                        }
                    } else {
                        foreach ($filterableAttributes as $index => $attribute) {
                            if ($attribute->getAttributeCode() == $filterObject->getCode()) {
                                $filters[] = $this->layerFilterFactory->create($filterObject, $layer, $attribute);
                                unset($filterableAttributes[$index]);
                                break;
                            }
                        }
                    }
                }
            }
            $this->filters = $filters;
        }
        return $this->filters;
    }

    /**
     * Get filter data objects
     *
     * @return FilterInterface[]
     */
    abstract protected function getFilterDataObjects();

    /**
     * Check if the specified filter type is a custom filter type
     *
     * @param string $filterType
     * @return bool
     */
    private function isCustomFilter($filterType)
    {
        return in_array($filterType, FilterInterface::CUSTOM_FILTER_TYPES);
    }

    /**
     * Check if type custom filter type is available
     *
     * @param string $filterType
     * @return bool
     */
    private function isAvailableCustomFilter($filterType)
    {
        if (!$this->customFilters) {
            $this->customFilters[] = FilterInterface::CATEGORY_FILTER;
            if ($this->config->isNewFilterEnabled()) {
                $this->customFilters[] = FilterInterface::NEW_FILTER;
            }
            if ($this->config->isInStockFilterEnabled()) {
                $this->customFilters[] = FilterInterface::STOCK_FILTER;
            }
            if ($this->config->isOnSaleFilterEnabled()) {
                $this->customFilters[] = FilterInterface::SALES_FILTER;
            }
        }

        return in_array($filterType, $this->customFilters);
    }
}
