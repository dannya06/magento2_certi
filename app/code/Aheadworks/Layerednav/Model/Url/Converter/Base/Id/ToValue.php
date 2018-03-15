<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url\Converter\Base\Id;

use Aheadworks\Layerednav\App\Request\AttributeList;
use Aheadworks\Layerednav\App\Request\CategoryList;
use Aheadworks\Layerednav\Model\Url\ConverterInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\Filter\FilterManager;

/**
 * Class ToValue
 * @package Aheadworks\Layerednav\Model\Url\Converter\Base\Id
 */
class ToValue implements ConverterInterface
{
    /**
     * @var AttributeList
     */
    private $attributeList;

    /**
     * @var CategoryList
     */
    private $categoryList;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @param AttributeList $attributeList
     * @param CategoryList $categoryList
     * @param FilterManager $filterManager
     */
    public function __construct(
        AttributeList $attributeList,
        CategoryList $categoryList,
        FilterManager $filterManager
    ) {
        $this->attributeList = $attributeList;
        $this->categoryList = $categoryList;
        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritdoc}
     */
    public function convertFilterParams($params)
    {
        /** @var array $attribute */
        foreach ($this->attributeList->getAttributesKeyedByCode() as $attributeCode => $attribute) {
            if (array_key_exists($attributeCode, $params)) {
                $optionValues = [];
                $paramValues = explode(',', $params[$attributeCode]);
                $options = $attribute['select_options'];
                foreach ($options as $option) {
                    if (in_array($option['value'], $paramValues)) {
                        $optionValues[] = preg_replace_callback(
                            '/-{1,}/',
                            function (array $matches) {
                                return $matches[0] . '-';
                            },
                            $this->filterManager->translitUrl(urlencode($option['label']))
                        );
                    }
                }
                if (!empty($optionValues)) {
                    $params[$attributeCode] = implode('-', $optionValues);
                }
            }
        }
        foreach ($this->attributeList->getAttributeCodes(AttributeList::LIST_TYPE_DECIMAL) as $attributeCode) {
            if (array_key_exists($attributeCode, $params)) {
                $paramValues = explode(',', $params[$attributeCode]);
                $params[$attributeCode] = implode('--', $paramValues);
            }
        }
        if (array_key_exists('cat', $params)) {
            $paramValues = explode(',', $params['cat']);
            /** @var array $categoriesKeyedById */
            $categoriesKeyedById = $this->categoryList->getCategoriesKeyedById();

            $categoryIds = [];
            foreach ($paramValues as $value) {
                if (isset($categoriesKeyedById[$value])) {
                    $category = $categoriesKeyedById[$value];
                    $categoryIds[] = preg_replace_callback(
                        '/-{1,}/',
                        function (array $matches) {
                            return $matches[0] . '-';
                        },
                        $category['url_key']
                    );
                }
            }
            if (!empty($categoryIds)) {
                unset($params['cat']);
                $params['cat'] = implode('-', $categoryIds);
            }
        }
        return $params;
    }
}
