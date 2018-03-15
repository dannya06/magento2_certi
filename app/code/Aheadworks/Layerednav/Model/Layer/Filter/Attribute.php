<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter\Attribute as ResourceAttribute;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\ConditionRegistry;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder as ItemDataBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Filter\FilterManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Attribute Filter
 *
 * @method int getStorefrontDisplayState()
 * @method AbstractFilter setStorefrontDisplayState(int $storefrontDisplayState)
 *
 * @package Aheadworks\Layerednav\Model\Layer\Filter
 */
class Attribute extends AbstractFilter
{
    /**
     * @var ResourceAttribute
     */
    private $resource;

    /**
     * @var StringUtils
     */
    private $stringUtils;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var ConditionRegistry
     */
    private $conditionsRegistry;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @param ItemFactory $filterItemFactory
     * @param StoreManagerInterface $storeManager
     * @param Layer $layer
     * @param ItemDataBuilder $itemDataBuilder
     * @param ResourceAttribute $resource
     * @param StringUtils $stringUtils
     * @param FilterManager $filterManager
     * @param ConditionRegistry $conditionsRegistry
     * @param Config $config
     * @param PageTypeResolver $pageTypeResolver
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        ItemDataBuilder $itemDataBuilder,
        ResourceAttribute $resource,
        StringUtils $stringUtils,
        FilterManager $filterManager,
        ConditionRegistry $conditionsRegistry,
        Config $config,
        PageTypeResolver $pageTypeResolver,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->resource = $resource;
        $this->stringUtils = $stringUtils;
        $this->filterManager = $filterManager;
        $this->conditionsRegistry = $conditionsRegistry;
        $this->config = $config;
        $this->pageTypeResolver = $pageTypeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(RequestInterface $request)
    {
        $filter = $request->getParam($this->_requestVar);

        if (is_array($filter)) {
            return $this;
        }

        $text = $this->getOptionText($filter);
        if ($filter && $text) {
            $this->resource->joinFilterToCollection($this);
            $this->conditionsRegistry->addConditions(
                $this->getAttributeModel()->getAttributeCode(),
                $this->resource->getWhereConditions($this, $filter)
            );
            $this->getLayer()
                ->getState()
                ->addFilter($this->_createItem($text, $filter));
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection */
        $collection = $this->getLayer()->getProductCollection();
        $options = $attribute->getFrontend()->getSelectOptions();

        // In one query, you can not use the temporary table twice
        $optionsCount = $this->resource->getCount($this);
        $parentCount = $collection->getFacetedData($attribute->getAttributeCode());
        foreach ($parentCount as $option) {
            $parentCount[$option['value']] = [
                'value' => $option['value'],
                'count' => '0'
            ];
            if (array_key_exists($option['value'], $optionsCount)) {
                $parentCount[$option['value']] = $optionsCount[$option['value']];
            }
        }
        $optionsCount = $parentCount;

        foreach ($options as $option) {
            if (is_array($option['value'])) {
                continue;
            }
            if ($this->stringUtils->strlen($option['value'])) {
                $optionLabel = $this->filterManager->stripTags($option['label']);
                $optionValue = $this->isReplaceValueByText()
                    ? $this->filterManager->translitUrl(urlencode($option['label']))
                    : $option['value'];
                // Check filter type
                if ($this->getAttributeIsFilterable($attribute) == self::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS) {
                    if (array_key_exists($option['value'], $optionsCount)
                        && ($optionsCount[$option['value']]['count'] || $optionsCount[$option['value']]['count'] == '0')
                    ) {
                        $this->itemDataBuilder->addItemData(
                            $optionLabel,
                            $optionValue,
                            $optionsCount[$option['value']]['count']
                        );
                    }
                } else {
                    $this->itemDataBuilder->addItemData(
                        $optionLabel,
                        $optionValue,
                        isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0
                    );
                }
            }
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * {@inheritdoc}
     */
    protected function _createItem($label, $value, $count = 0)
    {
        if ($this->isReplaceValueByText()) {
            $filterText = [];
            $rawFilterText = is_array($label) ? $label : [$label];
            foreach ($rawFilterText as $rawOptionText) {
                $filterText[] = $this->filterManager->translitUrl(urlencode($rawOptionText));
            }
            return parent::_createItem($label, implode(',', $filterText), $count);
        } else {
            return parent::_createItem($label, $value, $count);
        }
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
