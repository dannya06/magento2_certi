<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Model\Filter;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\Store;

/**
 * Class FormDataProcessor
 * @package Aheadworks\Layerednav\Model\Filter
 */
class FormDataProcessor
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Get prepared form data
     *
     * @param FilterInterface $filter
     * @param int $storeId
     * @return array
     */
    public function getPreparedFormData($filter, $storeId)
    {
        $preparedData = $this->dataObjectProcessor->buildOutputDataArray(
            $filter,
            FilterInterface::class
        );

        $preparedData['store_id'] = $storeId;

        $preparedData = array_merge($preparedData, $this->getPreparedTitle($filter, $storeId));
        $preparedData = array_merge($preparedData, $this->getPreparedDisplayState($filter, $storeId));
        $preparedData = array_merge($preparedData, $this->getPreparedSortOrder($filter, $storeId));

        if ($filter->getType() == FilterInterface::CATEGORY_FILTER) {
            $preparedData = array_merge($preparedData, $this->getPreparedCategoryListStyle($filter, $storeId));
        }

        $preparedData = $this->convertToString(
            $preparedData,
            [
                FilterInterface::EXCLUDE_CATEGORY_IDS
            ]
        );

        return $preparedData;
    }

    /**
     * Get prepared title
     *
     * @param FilterInterface $filter
     * @param int $storeId
     * @return array
     */
    private function getPreparedTitle($filter, $storeId)
    {
        $preparedData = [];

        $preparedData['title'] = $filter->getDefaultTitle();

        if ($storeId == Store::DEFAULT_STORE_ID) {
            $preparedData['default_title_checkbox'] = '0';
        } else {
            $preparedData['default_title_checkbox'] = '1';
            foreach ($filter->getStorefrontTitles() as $storefrontTitle) {
                if ($storefrontTitle->getStoreId() == $storeId) {
                    $preparedData['default_title_checkbox'] = '0';
                    $preparedData['title'] = $storefrontTitle->getValue();
                }
            }
        }

        return $preparedData;
    }

    /**
     * Get prepared display state data
     *
     * @param FilterInterface $filter
     * @param int $storeId
     * @return array
     */
    private function getPreparedDisplayState($filter, $storeId)
    {
        $preparedData = [];

        $preparedData['default_display_state'] = '1';
        foreach ($filter->getDisplayStates() as $displayState) {
            if ($displayState->getStoreId() == $storeId) {
                $preparedData['default_display_state'] = '0';
                $preparedData['display_state'] = $displayState->getValue();
            }
        }

        return $preparedData;
    }

    /**
     * Get prepared sort order data
     *
     * @param FilterInterface $filter
     * @param int $storeId
     * @return array
     */
    private function getPreparedSortOrder($filter, $storeId)
    {
        $preparedData = [];

        foreach ($filter->getSortOrders() as $sortOrder) {
            if ($sortOrder->getStoreId() == $storeId) {
                $preparedData['default_sort_order'] = '0';
                $preparedData['sort_order'] = $sortOrder->getValue();
            }
        }

        return $preparedData;
    }

    /**
     * Get prepared sort order data
     *
     * @param FilterInterface|Filter $filter
     * @param int $storeId
     * @return array
     */
    private function getPreparedCategoryListStyle($filter, $storeId)
    {
        $preparedData = [];

        if ($filter->getCategoryFilterData()) {
            /** @var FilterCategoryInterface|null $filterCategory */
            $filterCategory = $filter->getCategoryFilterData();

            /** @var StoreValueInterface $listStyle */
            foreach ($filterCategory->getListStyles() as $listStyle) {
                $preparedData['category_list_styles'][] = [
                    'store_id' => $listStyle->getStoreId(),
                    'value' => $listStyle->getValue()
                ];
                if ($listStyle->getStoreId() == $storeId) {
                    $preparedData['default_category_list_style'] = '0';
                    $preparedData['category_list_style'] = $listStyle->getValue();
                }
            }
        }

        return $preparedData;
    }

    /**
     * Convert selected fields to string
     *
     * @param array $data
     * @param string[] $fields
     * @return array
     */
    private function convertToString($data, $fields)
    {
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                if (is_array($data[$field])) {
                    foreach ($data[$field] as $key => $value) {
                        if ($value === false) {
                            $data[$field][$key] = '0';
                        } else {
                            $data[$field][$key] = (string)$value;
                        }
                    }
                } else {
                    $data[$field] = (string)$data[$field];
                }
            }
        }
        return $data;
    }
}
