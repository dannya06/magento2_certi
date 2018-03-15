<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\FilterListSearch;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\FilterFactory as LayerFilterFactory;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterface;
use Aheadworks\Layerednav\Model\Filter\CategoryValidator as FilterCategoryValidator;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\FilterListSearch
 */
class FilterListSearchTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterListSearch
     */
    private $filterListSearch;

    /**
     * @var LayerFilterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerFilterFactoryMock;

    /**
     * @var FilterableAttributeListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterableAttributesMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var FilterCategoryValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCategoryValidatorMock;

    /**
     * @var FilterRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaBuilderMock;

    /**
     * @var SortOrderBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sortOrderBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->layerFilterFactoryMock = $this->getMockBuilder(LayerFilterFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterableAttributesMock = $this->getMockBuilder(FilterableAttributeListInterface::class)
            ->getMockForAbstractClass();

        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['isNewFilterEnabled', 'isInStockFilterEnabled', 'isOnSaleFilterEnabled'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterCategoryValidatorMock = $this->getMockBuilder(FilterCategoryValidator::class)
            ->setMethods(['validate'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterRepositoryMock = $this->getMockBuilder(FilterRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->sortOrderBuilderMock = $this->getMockBuilder(SortOrderBuilder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterListSearch = $objectManager->getObject(
            FilterListSearch::class,
            [
                'layerFilterFactory' => $this->layerFilterFactoryMock,
                'filterableAttributes' => $this->filterableAttributesMock,
                'config' => $this->configMock,
                'filterCategoryValidator' => $this->filterCategoryValidatorMock,
                'filterRepository' => $this->filterRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'sortOrderBuilder' => $this->sortOrderBuilderMock
            ]
        );
    }

    /**
     * Test getFilters method
     *
     * @param string $filterType
     * @param bool $isValidForCategory
     * @param string $attributeCode
     * @param bool $emptyResult
     * @throws \Exception
     * @dataProvider getFiltersDataProvider
     */
    public function testGetFilters($filterType, $isValidForCategory, $attributeCode, $emptyResult)
    {
        $categoryModelMock = $this->getMockBuilder(CategoryModel::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $layerMock->expects($this->once())
            ->method('getCurrentCategory')
            ->willReturn($categoryModelMock);

        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->with(FilterInterface::POSITION)
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setAscendingDirection')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($sortOrderMock);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(FilterInterface::IS_FILTERABLE_IN_SEARCH, 0, 'gt')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addSortOrder')
            ->with($sortOrderMock)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $filterObjectMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();

        $filterSearchResultsMock = $this->getMockBuilder(FilterSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $filterSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$filterObjectMock]);
        $this->filterRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($filterSearchResultsMock);

        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterableAttributesMock->expects($this->once())
            ->method('getList')
            ->willReturn([$attributeMock]);

        $this->filterCategoryValidatorMock->expects($this->once())
            ->method('validate')
            ->with($filterObjectMock, $categoryModelMock)
            ->willReturn($isValidForCategory);

        if ($isValidForCategory) {
            $filterObjectMock->expects($this->atLeastOnce())
                ->method('getType')
                ->willReturn($filterType);

            $filterMock = $this->getMockBuilder(AbstractFilter::class)
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();

            if (in_array($filterType, FilterInterface::CUSTOM_FILTER_TYPES)) {
                $this->configMock->expects($this->once())
                    ->method('isNewFilterEnabled')
                    ->willReturn(true);
                $this->configMock->expects($this->once())
                    ->method('isInStockFilterEnabled')
                    ->willReturn(false);
                $this->configMock->expects($this->once())
                    ->method('isOnSaleFilterEnabled')
                    ->willReturn(false);

                if (!$emptyResult) {
                    $this->layerFilterFactoryMock->expects($this->once())
                        ->method('create')
                        ->with($filterObjectMock, $layerMock)
                        ->willReturn($filterMock);
                }
            } else {
                $filterObjectMock->expects($this->once())
                    ->method('getCode')
                    ->willReturn($attributeCode);
                $attributeMock->expects($this->once())
                    ->method('getAttributeCode')
                    ->willReturn($attributeCode);

                $this->layerFilterFactoryMock->expects($this->once())
                    ->method('create')
                    ->with($filterObjectMock, $layerMock, $attributeMock)
                    ->willReturn($filterMock);
            }
        }

        if ($emptyResult) {
            $this->assertEquals([], $this->filterListSearch->getFilters($layerMock));
        } else {
            $this->assertEquals([$filterMock], $this->filterListSearch->getFilters($layerMock));
        }
    }

    public function getFiltersDataProvider()
    {
        return [
            [
                'filterType'            => FilterInterface::ATTRIBUTE_FILTER,
                'isValidForCategory'    => true,
                'attributeCode'         => 'color',
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::ATTRIBUTE_FILTER,
                'isValidForCategory'    => false,
                'attributeCode'         => 'color',
                'emptyResult'           => true
            ],
            [
                'filterType'            => FilterInterface::PRICE_FILTER,
                'isValidForCategory'    => true,
                'attributeCode'         => 'price',
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::DECIMAL_FILTER,
                'isValidForCategory'    => true,
                'attributeCode'         => 'cost',
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::CATEGORY_FILTER,
                'isValidForCategory'    => true,
                'attributeCode'         => null,
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::NEW_FILTER,
                'isValidForCategory'    => true,
                'attributeCode'         => null,
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::STOCK_FILTER,
                'isValidForCategory'    => true,
                'attributeCode'         => null,
                'emptyResult'           => true
            ],
            [
                'filterType'            => FilterInterface::SALES_FILTER,
                'isValidForCategory'    => true,
                'attributeCode'         => null,
                'emptyResult'           => true
            ],
        ];
    }
}
