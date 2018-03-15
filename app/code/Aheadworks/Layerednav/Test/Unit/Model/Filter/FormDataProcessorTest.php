<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Model\Filter\FormDataProcessor;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\FormDataProcessor
 */
class FormDataProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormDataProcessor
     */
    private $model;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var array
     */
    private $processedData = [
        'store_id' => 1,
        'type' => FilterInterface::CATEGORY_FILTER,
        'default_title_checkbox' => '1',
        'title' => 'Test title',
        'default_title' => 'Test title',
        'storefront_titles' => [],
        'default_display_state' => '1',
        'storefront_display_state' => null,
        'display_states' => [],
        'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
        'default_sort_order' => '0',
        'storefront_sort_order' => FilterInterface::SORT_ORDER_MANUAL,
        'sort_orders' => [
            [
                'store_id' => 0,
                'value' => FilterInterface::SORT_ORDER_MANUAL,
            ]
        ],
        'category_mode' => FilterInterface::CATEGORY_MODE_ALL,
        'exclude_category_ids' => [],
        'default_category_list_style' => '0',
        'category_list_style' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH,
        'category_list_styles' => [
            [
                'store_id' => '1',
                'value' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH,
            ]
        ]
    ];

    /**
     * @var array
     */
    private $filterData = [
        FilterInterface::TYPE => FilterInterface::CATEGORY_FILTER,
        FilterInterface::DEFAULT_TITLE => 'Test title',
        FilterInterface::STOREFRONT_TITLES => [],
        FilterInterface::STOREFRONT_DISPLAY_STATE => null,
        FilterInterface::DISPLAY_STATES => [],
        FilterInterface::STOREFRONT_SORT_ORDER => FilterInterface::SORT_ORDER_MANUAL,
        FilterInterface::SORT_ORDERS =>
            [
                [
                    StoreValueInterface::STORE_ID => 0,
                    StoreValueInterface::VALUE => FilterInterface::SORT_ORDER_MANUAL,
                ],
            ],
        FilterInterface::CATEGORY_MODE => FilterInterface::CATEGORY_MODE_ALL,
        FilterInterface::EXCLUDE_CATEGORY_IDS => [],
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            FormDataProcessor::class,
            [
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
            ]
        );
    }

    /**
     * Test getPreparedFormData method
     */
    public function testGetPreparedFormData()
    {
        $storeId = 1;
        $defaultTitle = 'Test title';

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->setMethods(['getCategoryFilterData'])
            ->getMockForAbstractClass();

        $filterMock->expects($this->once())
            ->method('getType')
            ->willReturn(FilterInterface::CATEGORY_FILTER);
        $filterMock->expects($this->once())
            ->method('getDefaultTitle')
            ->willReturn($defaultTitle);
        $filterMock->expects($this->once())
            ->method('getStorefrontTitles')
            ->willReturn([]);
        $filterMock->expects($this->once())
            ->method('getDisplayStates')
            ->willReturn([]);

        $storeValueMock = $this->getMockBuilder(StoreValueInterface::class)
            ->getMockForAbstractClass();
        $storeValueMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $storeValueMock->expects($this->once())
            ->method('getValue')
            ->willReturn(FilterInterface::SORT_ORDER_MANUAL);
        $filterMock->expects($this->once())
            ->method('getSortOrders')
            ->willReturn([$storeValueMock]);

        $filterCategoryMock = $this->getMockBuilder(FilterCategoryInterface::class)
            ->getMockForAbstractClass();

        $filterCategoryStoreValueMock = $this->getMockBuilder(StoreValueInterface::class)
            ->getMockForAbstractClass();
        $filterCategoryStoreValueMock->expects($this->atLeastOnce())
            ->method('getStoreId')
            ->willReturn($storeId);
        $filterCategoryStoreValueMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn(FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH);

        $filterCategoryMock->expects($this->once())
            ->method('getListStyles')
            ->willReturn([$filterCategoryStoreValueMock]);

        $filterMock->expects($this->atLeastOnce())
            ->method('getCategoryFilterData')
            ->willReturn($filterCategoryMock);

        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($filterMock, FilterInterface::class)
            ->willReturn($this->filterData);

        $this->assertEquals($this->processedData, $this->model->getPreparedFormData($filterMock, $storeId));
    }
}
