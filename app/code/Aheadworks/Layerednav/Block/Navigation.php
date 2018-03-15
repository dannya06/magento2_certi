<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Applier;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\Layer\DataSource\CompositeConfigProvider;
use Aheadworks\Layerednav\Model\Layer\FilterListAbstract;
use Aheadworks\Layerednav\Model\Layer\FilterListResolver;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\AvailabilityFlagInterface;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Layer\Filter\FilterInterface as LayerFilterInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Navigation
 * @package Aheadworks\Layerednav\Block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Navigation extends Template
{
    /**
     * @var Layer
     */
    private $layer;

    /**
     * @var FilterListAbstract
     */
    private $filterList;

    /**
     * @var AvailabilityFlagInterface
     */
    private $visibilityFlag;

    /**
     * @var Applier
     */
    private $applier;

    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CompositeConfigProvider
     */
    private $dataSourceConfigProvider;

    /**
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param FilterListResolver $filterListResolver
     * @param AvailabilityFlagInterface $visibilityFlag
     * @param Applier $applier
     * @param PageTypeResolver $pageTypeResolver
     * @param Config $config
     * @param CompositeConfigProvider $dataSourceConfigProvider
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        FilterListResolver $filterListResolver,
        AvailabilityFlagInterface $visibilityFlag,
        Applier $applier,
        PageTypeResolver $pageTypeResolver,
        Config $config,
        CompositeConfigProvider $dataSourceConfigProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layer = $layerResolver->get();
        $this->filterList = $filterListResolver->get();
        $this->visibilityFlag = $visibilityFlag;
        $this->applier = $applier;
        $this->pageTypeResolver = $pageTypeResolver;
        $this->config = $config;
        $this->dataSourceConfigProvider = $dataSourceConfigProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->applier->applyFilters($this->layer);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if (!$this->visibilityFlag->isEnabled($this->layer, $this->getFilters())) {
            return '';
        }
        return parent::toHtml();
    }

    /**
     * Get filters
     *
     * @return Layer\Filter\AbstractFilter[]
     */
    public function getFilters()
    {
        return $this->filterList->getFilters($this->layer);
    }

    /**
     * Check if block has active filters
     *
     * @return bool
     */
    public function hasActiveFilters()
    {
        return !empty($this->layer->getState()->getFilters());
    }

    /**
     * Check if AJAX enabled on storefront
     *
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return $this->config->isAjaxEnabled();
    }

    /**
     * Get data source config
     *
     * @return array
     */
    public function getDataSourceConfig()
    {
        return $this->dataSourceConfigProvider->getConfig();
    }

    /**
     * Check if "Show X Items" Pop-over disabled
     *
     * @return bool
     */
    public function isPopoverDisabled()
    {
        return $this->config->isPopoverDisabled();
    }

    /**
     * Check if use attribute value instead of Id in url build logic
     *
     * @return bool
     */
    public function isUseAttrValueInsteadOfId()
    {
        return $this->config->getSeoFriendlyUrlOption() == SeoFriendlyUrl::ATTRIBUTE_VALUE_INSTEAD_OF_ID
            && $this->pageTypeResolver->getType() != PageTypeResolver::PAGE_TYPE_CATALOG_SEARCH;
    }

    /**
     * Check if use attribute values as subcategories in url build logic
     *
     * @return bool
     */
    public function isUseSubcategoriesAsAttrValues()
    {
        return $this->config->getSeoFriendlyUrlOption() == SeoFriendlyUrl::ATTRIBUTE_VALUE_AS_SUBCATEGORY
            && $this->pageTypeResolver->getType() != PageTypeResolver::PAGE_TYPE_CATALOG_SEARCH;
    }

    /**
     * Get page layout
     *
     * @return string
     */
    public function getPageLayout()
    {
        return $this->pageConfig->getPageLayout() ?: $this->getLayout()->getUpdate()->getPageLayout();
    }

    /**
     * Can show filter
     *
     * @param Layer\Filter\AbstractFilter $filter
     * @return bool
     */
    public function canShowFilter($filter)
    {
        $filterItems = $filter->getItems();
        if (!$filterItems) {
            return false;
        } elseif (!$this->config->hideEmptyFilters()) {
            return true;
        } else {
            foreach ($filterItems as $filterItem) {
                if ($filterItem->getCount()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if filter is expanded
     *
     * @param LayerFilterInterface $filter
     * @return bool
     */
    public function isFilterExpanded($filter)
    {
        return ($this->getPageLayout() != '1column')
            && ($filter->getStorefrontDisplayState() == FilterInterface::DISPLAY_STATE_EXPANDED);
    }

    /**
     * Check if the filter is active
     *
     * @param LayerFilterInterface $filter
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isFilterActive($filter)
    {
        $activeFilterItems = $this->layer->getState()->getFilters();
        if (!empty($activeFilterItems)) {
            foreach ($activeFilterItems as $activeItem) {
                if ($activeItem->getFilter()->getRequestVar() == $filter->getRequestVar()) {
                    return true;
                }
            }
        }
        return false;
    }
}
