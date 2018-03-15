<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Store\Model\Store;

/**
 * Class PostDataProcessor
 * @package Aheadworks\Layerednav\Model\Filter
 */
class PostDataProcessor
{
    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        $storeId = $data['store_id'];

        $data = $this->processTitle($data, $storeId);
        $data = $this->processDisplayState($data, $storeId);
        $data = $this->processSortOrder($data, $storeId);
        $data = $this->processCategoryMode($data);
        $data = $this->processCategoryFilterData($data, $storeId);

        return $data;
    }

    /**
     * Process title
     *
     * @param array $data
     * @param int $storeId
     * @return array
     */
    private function processTitle($data, $storeId)
    {
        if ($storeId == Store::DEFAULT_STORE_ID) {
            $data['default_title'] = $data['title'];
        } else {
            if (isset($data['storefront_titles'])) {
                foreach ($data['storefront_titles'] as $index => $sortOrder) {
                    if ($sortOrder['store_id'] == $storeId) {
                        unset($data['storefront_titles'][$index]);
                    }
                }
            }
            if (!isset($data['default_title_checkbox']) || !$data['default_title_checkbox']) {
                $data['storefront_titles'][] = [
                    'store_id' => $data['store_id'],
                    'value' => $data['title']
                ];
            }
        }

        return $data;
    }

    /**
     * Process display state
     *
     * @param array $data
     * @param int $storeId
     * @return array
     */
    private function processDisplayState($data, $storeId)
    {
        if (isset($data['display_states'])) {
            foreach ($data['display_states'] as $index => $displayState) {
                if ($displayState['store_id'] == $storeId) {
                    unset($data['display_states'][$index]);
                }
            }
        }
        if (!isset($data['default_display_state']) || !$data['default_display_state']) {
            $data['display_states'][] = [
                'store_id' => $data['store_id'],
                'value' => $data['display_state']
            ];
        }

        return $data;
    }

    /**
     * Process sort order
     *
     * @param array $data
     * @param int $storeId
     * @return array
     */
    private function processSortOrder($data, $storeId)
    {
        if (isset($data['sort_orders'])) {
            foreach ($data['sort_orders'] as $index => $sortOrder) {
                if ($sortOrder['store_id'] == $storeId) {
                    unset($data['sort_orders'][$index]);
                }
            }
        }
        if (!isset($data['default_sort_order']) || !$data['default_sort_order']) {
            $data['sort_orders'][] = [
                'store_id' => $data['store_id'],
                'value' => $data['sort_order']
            ];
        }

        return $data;
    }

    /**
     * Process category mode
     *
     * @param array $data
     * @return array
     */
    private function processCategoryMode($data)
    {
        if (isset($data['category_mode'])
            && $data['category_mode'] == FilterInterface::CATEGORY_MODE_EXCLUDE
            && !isset($data['exclude_category_ids'])) {
            $data['exclude_category_ids'] = [];
        }

        return $data;
    }

    /**
     * Process category filter data
     *
     * @param array $data
     * @param int $storeId
     * @return array
     */
    private function processCategoryFilterData($data, $storeId)
    {
        if ($data['type'] == FilterInterface::CATEGORY_FILTER) {
            if (isset($data['category_list_styles'])) {
                foreach ($data['category_list_styles'] as $index => $listStyle) {
                    if ($listStyle['store_id'] == $storeId) {
                        unset($data['category_list_styles'][$index]);
                    }
                }
            }
            if (!isset($data['default_category_list_style']) || !$data['default_category_list_style']) {
                $data['category_list_styles'][] = [
                    'store_id' => $data['store_id'],
                    'value' => $data['category_list_style']
                ];
            }
            $data['category_filter_data'][FilterCategoryInterface::LIST_STYLES] = $data['category_list_styles'];
        }

        unset($data['category_list_styles']);
        unset($data['category_list_style']);
        unset($data['default_category_list_style']);

        return $data;
    }
}
