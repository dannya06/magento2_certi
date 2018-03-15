<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\Layer\Filter\DataProvider\Category as DataProvider;
use Aheadworks\Layerednav\Model\Layer\Filter\DataProvider\CategoryFactory as DataProviderFactory;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\ConditionRegistry;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder as ItemDataBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Category Filter
 *
 * @method int getStorefrontDisplayState()
 * @method AbstractFilter setStorefrontDisplayState(int $storefrontDisplayState)
 * @method string getStorefrontListStyle()
 * @method $this setStorefrontListStyle(string $storefrontListStyle)
 *
 * @package Aheadworks\Layerednav\Model\Layer\Filter
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category extends AbstractFilter
{
    /**
     * Request var
     */
    const REQUEST_VAR = 'cat';

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var ConditionRegistry
     */
    private $conditionsRegistry;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $itemLabel;

    /**
     * @param ItemFactory $filterItemFactory
     * @param StoreManagerInterface $storeManager
     * @param Layer $layer
     * @param ItemDataBuilder $itemDataBuilder
     * @param DataProviderFactory $dataProviderFactory
     * @param ConditionRegistry $conditionsRegistry
     * @param Escaper $escaper
     * @param PageTypeResolver $pageTypeResolver
     * @param Config $config
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        ItemDataBuilder $itemDataBuilder,
        DataProviderFactory $dataProviderFactory,
        ConditionRegistry $conditionsRegistry,
        Escaper $escaper,
        PageTypeResolver $pageTypeResolver,
        Config $config,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->_requestVar = self::REQUEST_VAR;
        $this->dataProvider = $dataProviderFactory->create(['layer' => $layer]);
        $this->conditionsRegistry = $conditionsRegistry;
        $this->escaper = $escaper;
        $this->pageTypeResolver = $pageTypeResolver;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(RequestInterface $request)
    {
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        $filterParams = explode(',', $filter);
        $categoryIds = $this->dataProvider->validateFilter($filterParams);
        if (!$categoryIds) {
            return $this;
        }

        $this->dataProvider->getResource()->joinFilterToCollection($this);
        $this->conditionsRegistry->addConditions(
            'category',
            $this->dataProvider->getResource()->getWhereConditions($categoryIds)
        );

        $this->getLayer()
            ->getState()
            ->addFilter(
                $this->_createItem('category', $filter)
            );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __($this->itemLabel);
    }

    /**
     * Set filter name
     *
     * @param string $label
     * @return $this
     */
    public function setName($label)
    {
        $this->itemLabel = $label;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getItemsData()
    {
        if ($this->pageTypeResolver->getType() == PageTypeResolver::PAGE_TYPE_CATALOG_SEARCH) {
            return $this->getItemsDataForSearchPage();
        }
        return $this->getItemsDataForCategoryPage();
    }

    /**
     * {@inheritdoc}
     */
    protected function _createItem($label, $value, $count = 0)
    {
        if ($this->isReplaceValueByText()) {
            $categoryIds = explode(',', $value);
            $urlKeys = $this->dataProvider->getCategoryUrlKeys($categoryIds);
            return parent::_createItem($label, implode(',', $urlKeys), $count);
        } else {
            return parent::_createItem($label, $value, $count);
        }
    }

    /**
     * Get filter items data for category page
     *
     * @return array
     */
    private function getItemsDataForCategoryPage()
    {
        $category = $this->getLayer()->getCurrentCategory();
        $childCategories = $category->getChildrenCategories();
        $collection = $this->getLayer()->getProductCollection();
        $collection->addCountToCategories($childCategories);
        $resource = $this->dataProvider->getResource();

        if ($category->getIsActive()) {
            foreach ($childCategories as $category) {
                if ($category->getIsActive()) {
                    if ($productCount = $resource->getProductCount($this, $category)) {
                        $value = $this->isReplaceValueByText()
                            ? $category->getUrlKey()
                            : $category->getId();
                        $this->itemDataBuilder->addItemData(
                            $this->escaper->escapeHtml($category->getName()),
                            $value,
                            $productCount
                        );
                    }
                }
            }
        }
        return $this->itemDataBuilder->build();
    }

    /**
     * Get filter items data for search page
     *
     * @return array
     */
    private function getItemsDataForSearchPage()
    {
        $category = $this->getLayer()->getCurrentCategory();
        $childCategories = $category->getChildrenCategories();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection */
        $collection = $this->getLayer()->getProductCollection();
        $optionsFacetedData = $collection->getFacetedData('category');

        if ($category->getIsActive()) {
            foreach ($childCategories as $category) {
                if ($category->getIsActive()
                    && isset($optionsFacetedData[$category->getId()])
                ) {
                    $this->itemDataBuilder->addItemData(
                        $this->escaper->escapeHtml($category->getName()),
                        $category->getId(),
                        $optionsFacetedData[$category->getId()]['count']
                    );
                }
            }
        }
        return $this->itemDataBuilder->build();
    }

    /**
     * Check if option value should be replaced by url compatible text representation
     *
     * @return bool
     */
    private function isReplaceValueByText()
    {
        return $this->config->getSeoFriendlyUrlOption() != SeoFriendlyUrl::DEFAULT_OPTION
            && $this->pageTypeResolver->getType() != PageTypeResolver::PAGE_TYPE_CATALOG_SEARCH;
    }
}
