<?php
namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter\Attribute as ResourceAttribute;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\ConditionRegistry;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder as ItemDataBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filter\StripTags;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Attribute Filter
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
     * @var StripTags
     */
    private $tagFilter;

    /**
     * @var ConditionRegistry
     */
    private $conditionsRegistry;

    /**
     * @param ItemFactory $filterItemFactory
     * @param StoreManagerInterface $storeManager
     * @param Layer $layer
     * @param ItemDataBuilder $itemDataBuilder
     * @param ResourceAttribute $resource
     * @param StringUtils $stringUtils
     * @param StripTags $tagFilter
     * @param ConditionRegistry $conditionsRegistry
     * @param array $data
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        ItemDataBuilder $itemDataBuilder,
        ResourceAttribute $resource,
        StringUtils $stringUtils,
        StripTags $tagFilter,
        ConditionRegistry $conditionsRegistry,
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
        $this->tagFilter = $tagFilter;
        $this->conditionsRegistry = $conditionsRegistry;
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
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()
            ->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData($attribute->getAttributeCode());

        if (count($optionsFacetedData) === 0
            && $this->getAttributeIsFilterable($attribute) !== static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
        ) {
            return $this->itemDataBuilder->build();
        }

        $productSize = $productCollection->getSize();

        $options = $attribute->getFrontend()
            ->getSelectOptions();
        foreach ($options as $option) {
            if (empty($option['value'])) {
                continue;
            }

            $value = $option['value'];

            $count = isset($optionsFacetedData[$value]['count'])
                ? (int)$optionsFacetedData[$value]['count']
                : 0;
            // Check filter type
            if (
                $this->getAttributeIsFilterable($attribute) === static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
                && (!$this->isOptionReducesResults($count, $productSize) || $count === 0)
            ) {
                continue;
            }
            $this->itemDataBuilder->addItemData(
                $this->tagFilter->filter($option['label']),
                $value,
                $count
            );
        }

        return $this->itemDataBuilder->build();
    }
}
