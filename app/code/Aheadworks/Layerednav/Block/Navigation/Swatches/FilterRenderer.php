<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\Navigation\Swatches;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;
use Aheadworks\Layerednav\Model\PageTypeResolver;

/**
 * Class FilterRenderer
 * @package Aheadworks\Layerednav\Block\Navigation\Swatches
 */
class FilterRenderer extends RenderLayered
{
    /**
     * @var Layer
     */
    private $layer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Layerednav::layer/renderer/swatches/filter.phtml';

    /**
     * @param Context $context
     * @param Attribute $eavAttribute
     * @param AttributeFactory $layerAttribute
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Swatches\Helper\Media $mediaHelper
     * @param LayerResolver $layerResolver
     * @param Config $config
     * @param PageTypeResolver $pageTypeResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        LayerResolver $layerResolver,
        Config $config,
        PageTypeResolver $pageTypeResolver,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttribute,
            $swatchHelper,
            $mediaHelper,
            $data
        );
        $this->layer = $layerResolver->get();
        $this->config = $config;
        $this->pageTypeResolver = $pageTypeResolver;
    }

    /**
     * Check if filter item is active
     *
     * @param string $code
     * @param string $value
     * @return bool
     * @throws LocalizedException
     */
    public function isActiveItem($code, $value)
    {
        foreach ($this->layer->getState()->getFilters() as $filter) {
            $filterValues = explode(',', $filter->getValue());
            if ($filter->getFilter()->getRequestVar() == $code
                && false !== array_search($value, $filterValues)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSwatchData()
    {
        $optionIdsKeyedByValue = [];
        $options = [];
        foreach ($this->eavAttribute->getOptions() as $option) {
            $swatchOption = null;
            $currentOption = $this->getFilterOption($this->filter->getItems(), $option);
            $value = $this->isReplaceValueByText()
                ? $this->filterManager->translitUrl(urlencode($option->getLabel()))
                : $option->getValue();

            if ($currentOption) {
                $swatchOption = $currentOption;
            } elseif ($this->isShowEmptyResults()) {
                $swatchOption = $this->getUnusedOption($option);
            }
            if ($swatchOption) {
                $options[$value] = $swatchOption;
                $optionIdsKeyedByValue[$value] = $option->getValue();
            }
        }
        return [
            'attribute_id' => $this->eavAttribute->getId(),
            'attribute_code' => $this->eavAttribute->getAttributeCode(),
            'attribute_label' => $this->eavAttribute->getStoreLabel(),
            'options' => $options,
            'swatches' => $this->getSwatches($optionIdsKeyedByValue),
        ];
    }

    /**
     * Get swatches keyed by value
     *
     * @param array $optionIdsKeyedByValue
     * @return array
     */
    private function getSwatches(array $optionIdsKeyedByValue)
    {
        $result = [];
        $swatches = $this->swatchHelper->getSwatchesByOptionsId(
            array_values($optionIdsKeyedByValue)
        );
        foreach ($optionIdsKeyedByValue as $value => $optionId) {
            if (isset($swatches[$optionId])) {
                $result[$value] = $swatches[$optionId];
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterOption(array $filterItems, Option $swatchOption)
    {
        $resultOption = false;

        $filterItem = $this->isReplaceValueByText()
            ? $this->getFilterItemByText(
                $filterItems,
                $this->filterManager->translitUrl(urlencode($swatchOption->getLabel()))
            )
            : $this->getFilterItemById($filterItems, $swatchOption->getValue());
        if ($filterItem && $this->isOptionVisible($filterItem)) {
            $resultOption = $this->getOptionViewData($filterItem, $swatchOption);
            $resultOption['count'] = $filterItem->getCount();
        }

        return $resultOption;
    }

    /**
     * Get filter item by text
     *
     * @param array $filterItems
     * @param string $text
     * @return bool|FilterItem
     */
    private function getFilterItemByText(array $filterItems, $text)
    {
        foreach ($filterItems as $item) {
            if ($item->getValue() == $text) {
                return $item;
            }
        }
        return false;
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

    /**
     * Check if option should be displayed
     *
     * @param string $code
     * @param string $value
     * @param array $label
     * @return bool
     * @throws LocalizedException
     */
    public function isNeedToShowOption($code, $value, $label)
    {
        return !$this->config->hideEmptyAttributeValues()
            || ($this->isActiveItem($code, $value))
            || (isset($label['count']) && $label['count'] > 0);
    }
}
