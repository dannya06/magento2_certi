<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessor;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor
 */
class PostDataProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PostDataProcessor
     */
    private $model;

    /**
     * @var array
     */
    private $postData = [
        'store_id' => '1',
        'type' => FilterInterface::CATEGORY_FILTER,
        'title' => 'Test filter store',
        'default_title' => 'Test filter',
        'default_title_checkbox' => '0',
        'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
        'default_display_state' => '0',
        'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
        'default_sort_order' => '0',
        'category_mode' => FilterInterface::CATEGORY_MODE_EXCLUDE,
        'category_list_style' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH
    ];

    private $processedData = [
        'store_id' => '1',
        'type' => FilterInterface::CATEGORY_FILTER,
        'default_title' => 'Test filter',
        'default_title_checkbox' => '0',
        'title' => 'Test filter store',
        'storefront_titles' => [
            [
                'store_id' => '1',
                'value' => 'Test filter store'
            ],
        ],
        'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
        'default_display_state' => '0',
        'display_states' => [
            [
                'store_id' => '1',
                'value' => FilterInterface::DISPLAY_STATE_COLLAPSED
            ]
        ],
        'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
        'default_sort_order' => '0',
        'sort_orders' => [
            [
                'store_id' => '1',
                'value' => FilterInterface::SORT_ORDER_MANUAL
            ]
        ],
        'category_mode' => FilterInterface::CATEGORY_MODE_EXCLUDE,
        'exclude_category_ids' => [],
        'category_filter_data' => [
            FilterCategoryInterface::LIST_STYLES => [
                [
                    'store_id' => '1',
                    'value' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH
                ]
            ]
        ]
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(
            PostDataProcessor::class,
            []
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $this->assertEquals($this->processedData, $this->model->process($this->postData));
    }
}
