<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter\Custom\AbstractFilter as ResourceAbstractFilter;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\ConditionRegistry;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder as ItemDataBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Stdlib\StringUtils;

/**
 * Custom Filter
 *
 * @method string getSeoFriendlyValue()
 * @method AbstractFilter setSeoFriendlyValue(string $value)
 * @method int getStorefrontDisplayState()
 * @method AbstractFilter setStorefrontDisplayState(int $storefrontDisplayState)
 *
 * @package Aheadworks\Layerednav\Model\Layer\Filter
 */
class Custom extends AbstractFilter
{
    /**
     * @var ResourceAbstractFilter
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
     * @var string
     */
    private $itemLabel;

    /**
     * @param ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param ItemDataBuilder $itemDataBuilder
     * @param ConditionRegistry $conditionsRegistry
     * @param ResourceAbstractFilter $resource
     * @param StringUtils $stringUtils
     * @param FilterManager $filterManager
     * @param Config $config
     * @param PageTypeResolver $pageTypeResolver
     * @param string $requestVar
     * @param string $itemLabel
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        ItemDataBuilder $itemDataBuilder,
        ConditionRegistry $conditionsRegistry,
        ResourceAbstractFilter $resource,
        StringUtils $stringUtils,
        FilterManager $filterManager,
        Config $config,
        PageTypeResolver $pageTypeResolver,
        $requestVar,
        $itemLabel,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->conditionsRegistry = $conditionsRegistry;
        $this->resource = $resource;
        $this->stringUtils = $stringUtils;
        $this->filterManager = $filterManager;
        $this->config = $config;
        $this->pageTypeResolver = $pageTypeResolver;
        $this->itemLabel = $itemLabel;
        $this->setRequestVar($requestVar);
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

        if ($filter == '1') {
            $this->resource->joinFilterToCollection($this);
            foreach ($this->resource->getWhereConditions($this, $filter) as $attribute => $conditions) {
                $this->conditionsRegistry->addConditions($attribute, $conditions);
            }
            $this->getLayer()
                ->getState()
                ->addFilter($this->_createItem($this->itemLabel, $filter));
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getItemsData()
    {
        $options = $this->getOptions();
        $optionsCount = $this->resource->getCount($this);
        $parentCount = $this->resource->getParentCount($this);
        foreach (array_keys($parentCount) as $key) {
            if (empty($parentCount[$key])) {
                unset($parentCount[$key]);
                continue;
            }
            $parentCount[$key] = '0';
            if (array_key_exists($key, $optionsCount)) {
                $parentCount[$key] = $optionsCount[$key];
            }
        }
        $optionsCount = $parentCount;

        foreach ($options as $option) {
            if (is_array($option['value'])) {
                continue;
            }
            if ($this->stringUtils->strlen($option['value'])) {
                if (array_key_exists($option['value'], $optionsCount)
                    && ($optionsCount[$option['value']] || $optionsCount[$option['value']] == '0')
                ) {
                    $optionValue = $this->isReplaceValueByText()
                        ? $this->getSeoFriendlyValue()
                        : $option['value'];
                    $this->itemDataBuilder->addItemData(
                        $this->filterManager->stripTags($option['label']),
                        $optionValue,
                        $optionsCount[$option['value']]
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
        $value = $this->isReplaceValueByText()
            ? $this->getSeoFriendlyValue()
            : $value;
        return parent::_createItem($label, $value, $count);
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
     * @return array
     */
    private function getOptions()
    {
        return [
            [
                'value' => 1,
                'label' => $this->itemLabel
            ]
        ];
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
