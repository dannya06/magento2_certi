<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Aheadworks\Layerednav\Model\Layer\FilterFactory;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\Filter\Category as CategoryFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Attribute as AttributeFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Price as PriceFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Decimal as DecimalFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Custom\NewProduct as NewProductCustomFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Custom\Sales as SalesCustomFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Custom\Stock as StockCustomFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderPool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\FilterFactory
 */
class FilterFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var DataBuilderPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataBuilderPoolMock;

    /**
     * @var FilterRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)
            ->getMockForAbstractClass();

        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataBuilderPoolMock = $this->getMockBuilder(DataBuilderPool::class)
            ->setMethods(['getDataBuilder'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterRepositoryMock = $this->getMockBuilder(FilterRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->filterFactory = $objectManager->getObject(
            FilterFactory::class,
            [
                'objectManager' => $this->objectManagerMock,
                'config' => $this->configMock,
                'dataBuilderPool' => $this->dataBuilderPoolMock,
                'filterRepository' => $this->filterRepositoryMock
            ]
        );
    }

    /**
     * Test create method if custom filter will be created
     *
     * @param string $filterType
     * @param string $filterClass
     * @param int $filterDisplayState
     * @param int $configDisplayState
     * @param string $sortOrder
     * @throws \Exception
     * @dataProvider createCustomFilterDataProvider
     */
    public function testCreateCustomFilter(
        $filterType,
        $filterClass,
        $filterDisplayState,
        $configDisplayState,
        $sortOrder
    ) {
        $filterObjectId = 1;
        $filterTitle = 'Custom Filter Title';

        $filterObjectMock = $this->getMockBuilder(FilterInterface::class)
            ->setMethods(['getCategoryFilterData'])
            ->getMockForAbstractClass();
        $filterObjectMock->expects($this->exactly(4))
            ->method('getType')
            ->willReturn($filterType);
        $filterObjectMock->expects($this->once())
            ->method('getStorefrontDisplayState')
            ->willReturn($filterDisplayState);
        $filterObjectMock->expects($this->once())
            ->method('getStorefrontTitle')
            ->willReturn($filterTitle);

        $layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $customFilterMock = $this->getMockBuilder(AbstractFilter::class)
            ->setMethods(['setName', 'setSeoFriendlyValue', 'setStorefrontDisplayState', 'setStorefrontListStyle'])
            ->disableOriginalConstructor()
            ->getMock();
        $customFilterMock->expects($this->once())
            ->method('setName')
            ->with($filterTitle)
            ->willReturnSelf();
        $customFilterMock->expects($this->once())
            ->method('setSeoFriendlyValue')
            ->with($filterType)
            ->willReturnSelf();

        if ($filterType == FilterInterface::CATEGORY_FILTER) {
            $filterObjectMock->expects($this->once())
                ->method('getId')
                ->willReturn($filterObjectId);
            $this->filterRepositoryMock->expects($this->once())
                ->method('get')
                ->with($filterObjectId)
                ->willReturn($filterObjectMock);

            $categoryFilterMock = $this->getMockBuilder(FilterCategoryInterface::class)
                ->getMockForAbstractClass();
            $categoryFilterMock->expects($this->once())
                ->method('getStorefrontListStyle')
                ->willReturn(FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH);
            $filterObjectMock->expects($this->once())
                ->method('getCategoryFilterData')
                ->willReturn($categoryFilterMock);
            $customFilterMock->expects($this->once())
                ->method('setStorefrontListStyle')
                ->with(FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH)
                ->willReturnSelf();
        }

        if (!$filterDisplayState) {
            $this->configMock->expects($this->once())
                ->method('getFilterDisplayState')
                ->willReturn($configDisplayState);
            $customFilterMock->expects($this->once())
                ->method('setStorefrontDisplayState')
                ->with($configDisplayState)
                ->willReturnSelf();
        } else {
            $customFilterMock->expects($this->once())
                ->method('setStorefrontDisplayState')
                ->with($filterDisplayState)
                ->willReturnSelf();
        }

        $filterObjectMock->expects($this->once())
            ->method('getStorefrontSortOrder')
            ->willReturn($sortOrder);

        $dataBuilderMock = $this->getMockBuilder(DataBuilderInterface::class)
            ->getMockForAbstractClass();

        $this->dataBuilderPoolMock->expects($this->once())
            ->method('getDataBuilder')
            ->with($sortOrder)
            ->willReturn($dataBuilderMock);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $filterClass,
                [
                    'layer' => $layerMock,
                    'itemDataBuilder' => $dataBuilderMock
                ]
            )
            ->willReturn($customFilterMock);

        $this->assertEquals($customFilterMock, $this->filterFactory->create($filterObjectMock, $layerMock));
    }

    /**
     * @return array
     */
    public function createCustomFilterDataProvider()
    {
        return [
            'category filter' => [
                FilterInterface::CATEGORY_FILTER,
                CategoryFilter::class,
                FilterInterface::DISPLAY_STATE_EXPANDED,
                FilterInterface::DISPLAY_STATE_COLLAPSED,
                FilterInterface::SORT_ORDER_MANUAL,
            ],
            'category filter - no display state' => [
                FilterInterface::CATEGORY_FILTER,
                CategoryFilter::class,
                null,
                FilterInterface::DISPLAY_STATE_COLLAPSED,
                FilterInterface::SORT_ORDER_MANUAL,
            ],
            'new product filter' => [
                FilterInterface::NEW_FILTER,
                NewProductCustomFilter::class,
                FilterInterface::DISPLAY_STATE_EXPANDED,
                FilterInterface::DISPLAY_STATE_COLLAPSED,
                FilterInterface::SORT_ORDER_MANUAL,
            ],
            'on sale filter' => [
                FilterInterface::SALES_FILTER,
                SalesCustomFilter::class,
                FilterInterface::DISPLAY_STATE_EXPANDED,
                FilterInterface::DISPLAY_STATE_COLLAPSED,
                FilterInterface::SORT_ORDER_MANUAL,
            ],
            'in stock filter' => [
                FilterInterface::STOCK_FILTER,
                StockCustomFilter::class,
                FilterInterface::DISPLAY_STATE_EXPANDED,
                FilterInterface::DISPLAY_STATE_COLLAPSED,
                FilterInterface::SORT_ORDER_MANUAL,
            ]
        ];
    }

    /**
     * Test create method if attribute filter will be created
     *
     * @param string $filterType
     * @param string $filterClass
     * @param int $filterDisplayState
     * @param int $configDisplayState
     * @param string|null $sortOrder
     * @throws \Exception
     * @dataProvider createAttributeFilterDataProvider
     */
    public function testCreateAttributeFilter(
        $filterType,
        $filterClass,
        $filterDisplayState,
        $configDisplayState,
        $sortOrder
    ) {
        $filterObjectMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterObjectMock->expects($this->exactly(2))
            ->method('getType')
            ->willReturn($filterType);
        $filterObjectMock->expects($this->once())
            ->method('getStorefrontDisplayState')
            ->willReturn($filterDisplayState);

        $layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $attributeFilterMock = $this->getMockBuilder(AbstractFilter::class)
            ->setMethods(['setStorefrontDisplayState'])
            ->disableOriginalConstructor()
            ->getMock();

        if (!$filterDisplayState) {
            $this->configMock->expects($this->once())
                ->method('getFilterDisplayState')
                ->willReturn($configDisplayState);
            $attributeFilterMock->expects($this->once())
                ->method('setStorefrontDisplayState')
                ->with($configDisplayState)
                ->willReturnSelf();
        } else {
            $attributeFilterMock->expects($this->once())
                ->method('setStorefrontDisplayState')
                ->with($filterDisplayState)
                ->willReturnSelf();
        }

        $filterObjectMock->expects($this->once())
            ->method('getStorefrontSortOrder')
            ->willReturn($sortOrder);

        $dataBuilderMock = $this->getMockBuilder(DataBuilderInterface::class)
            ->getMockForAbstractClass();

        $this->dataBuilderPoolMock->expects($this->once())
            ->method('getDataBuilder')
            ->with($sortOrder)
            ->willReturn($dataBuilderMock);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $filterClass,
                [
                    'data' => ['attribute_model' => $attributeMock],
                    'layer' => $layerMock,
                    'itemDataBuilder' => $dataBuilderMock
                ]
            )
            ->willReturn($attributeFilterMock);

        $this->assertEquals(
            $attributeFilterMock,
            $this->filterFactory->create($filterObjectMock, $layerMock, $attributeMock)
        );
    }

    /**
     * @return array
     */
    public function createAttributeFilterDataProvider()
    {
        return [
            'default filter' => [
                FilterInterface::ATTRIBUTE_FILTER,
                AttributeFilter::class,
                FilterInterface::DISPLAY_STATE_EXPANDED,
                FilterInterface::DISPLAY_STATE_COLLAPSED,
                FilterInterface::SORT_ORDER_ASC
            ],
            'default filter - no display state' => [
                FilterInterface::ATTRIBUTE_FILTER,
                AttributeFilter::class,
                null,
                FilterInterface::DISPLAY_STATE_COLLAPSED,
                FilterInterface::SORT_ORDER_DESC
            ],
            'price filter' => [
                FilterInterface::PRICE_FILTER,
                PriceFilter::class,
                FilterInterface::DISPLAY_STATE_EXPANDED,
                FilterInterface::DISPLAY_STATE_COLLAPSED,
                FilterInterface::SORT_ORDER_MANUAL
            ],
            'decimal filter' => [
                FilterInterface::DECIMAL_FILTER,
                DecimalFilter::class,
                FilterInterface::DISPLAY_STATE_EXPANDED,
                FilterInterface::DISPLAY_STATE_COLLAPSED,
                FilterInterface::SORT_ORDER_MANUAL
             ],
        ];
    }

    /**
     * Test create method if attribute filter will be created
     * @expectedException \Exception
     * @@expectedExceptionMessage No attribute specified!
     */
    public function testCreateAttributeFilterNoAttributeException()
    {
        $filterType =  FilterInterface::ATTRIBUTE_FILTER;

        $filterObjectMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterObjectMock->expects($this->once())
            ->method('getType')
            ->willReturn($filterType);

        $layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterFactory->create($filterObjectMock, $layerMock);
    }
}
