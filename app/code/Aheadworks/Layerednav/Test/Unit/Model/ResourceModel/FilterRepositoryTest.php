<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel;

use Aheadworks\Layerednav\Model\ResourceModel\FilterRepository;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterface;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterfaceFactory;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Collection as FilterCollection;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\CollectionFactory as FilterCollectionFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\FilterRepository
 */
class FilterRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterRepository
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var FilterInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterFactoryMock;

    /**
     * @var FilterCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCollectionFactoryMock;

    /**
     * @var FilterSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterSearchResultsFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['load', 'delete', 'save'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterFactoryMock = $this->getMockBuilder(FilterInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterCollectionFactoryMock = $this->getMockBuilder(FilterCollectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterSearchResultsFactoryMock = $this->getMockBuilder(FilterSearchResultsInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionAttributesJoinProcessorMock = $this->getMockBuilder(JoinProcessorInterface::class)
            ->getMockForAbstractClass();

        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->getMockForAbstractClass();

        $this->model = $objectManager->getObject(
            FilterRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'filterFactory' => $this->filterFactoryMock,
                'filterCollectionFactory' => $this->filterCollectionFactoryMock,
                'filterSearchResultsFactory' => $this->filterSearchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * Test save method
     */
    public function testSave()
    {
        $filterId = 1;
        $storeId = 1;

        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);
        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($filterMock)
            ->willReturn($filterMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->assertEquals($filterMock, $this->model->save($filterMock));
    }

    /**
     * Test save method if an error occurs
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Unknown error!
     */
    public function testSaveException()
    {
        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($filterMock)
            ->willThrowException(new \Exception('Unknown error!'));

        $this->model->save($filterMock);
    }

    /**
     * Test get method
     */
    public function testGet()
    {
        $filterId = 1;
        $storeId = 1;

        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->assertEquals($filterMock, $this->model->get($filterId));
    }

    /**
     * Test get method if specified filter does not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 2
     */
    public function testGetException()
    {
        $filterId = 2;
        $storeId = 1;

        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->model->get($filterId);
    }

    /**
     * Test getByCode method
     */
    public function testGetByCode()
    {
        $filterId = 1;
        $filterCode = 'color';
        $filterType = FilterInterface::ATTRIBUTE_FILTER;
        $storeId = 1;

        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $filterCollectionMock = $this->getMockBuilder(FilterCollection::class)
            ->setMethods(['addFilterByCode', 'addFilterByType', 'getFirstItem'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterCollectionMock->expects($this->once())
            ->method('addFilterByCode')
            ->with($filterCode)
            ->willReturnSelf();
        $filterCollectionMock->expects($this->once())
            ->method('addFilterByType')
            ->with($filterType)
            ->willReturnSelf();
        $filterCollectionMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($filterMock);
        $this->filterCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterCollectionMock);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->assertEquals($filterMock, $this->model->getByCode($filterCode, $filterType));
    }

    /**
     * Test getByCode method if specified filter does not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with code = color
     */
    public function testGetByCodeException()
    {
        $filterCode = 'color';
        $filterType = FilterInterface::ATTRIBUTE_FILTER;

        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);

        $filterCollectionMock = $this->getMockBuilder(FilterCollection::class)
            ->setMethods(['addFilterByCode', 'addFilterByType', 'getFirstItem'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterCollectionMock->expects($this->once())
            ->method('addFilterByCode')
            ->with($filterCode)
            ->willReturnSelf();
        $filterCollectionMock->expects($this->once())
            ->method('addFilterByType')
            ->with($filterType)
            ->willReturnSelf();
        $filterCollectionMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($filterMock);
        $this->filterCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterCollectionMock);

        $this->model->getByCode($filterCode, $filterType);
    }

    /**
     * Test getList method
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $filterName = 'code';
        $filterValue = 'color';
        $collectionSize = 5;
        $currentPage = 1;
        $pageSize = 2;
        $storeId = 1;

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->getMockForAbstractClass();
        $searchResultsMock = $this->getMockBuilder(FilterSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $searchResultsMock->expects($this->atLeastOnce())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->filterSearchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->getMockBuilder(FilterCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterCollectionFactoryMock
            ->method('create')
            ->willReturn($collectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, FilterInterface::class)
            ->willReturnSelf();

        $filterModelMock = $this->getMockBuilder(FilterModel::class)
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterModelMock->expects($this->once())
            ->method('getData')
            ->willReturn([
                'id' => 1,
                $filterName => $filterValue
            ]);

        $filterGroupMock = $this->getMockBuilder(FilterGroup::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filterMock = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterMock]);
        $filterMock->expects($this->once())
            ->method('getConditionType')
            ->willReturn(false);
        $filterMock->expects($this->atLeastOnce())
            ->method('getField')
            ->willReturn($filterName);
        $filterMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn($filterValue);
        $collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with([$filterName], [['eq' => $filterValue]]);
        $collectionMock
            ->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->atLeastOnce())
            ->method('getSortOrders')
            ->willReturn([$sortOrderMock]);
        $searchCriteriaMock->expects($this->atLeastOnce())
            ->method('getCurrentPage')
            ->willReturn($currentPage);
        $searchCriteriaMock->expects($this->atLeastOnce())
            ->method('getPageSize')
            ->willReturn($pageSize);
        $sortOrderMock->expects($this->once())
            ->method('getField')
            ->willReturn($filterName);
        $collectionMock->expects($this->once())
            ->method('addOrder')
            ->with($filterName, SortOrder::SORT_ASC);
        $sortOrderMock->expects($this->once())
            ->method('getDirection')
            ->willReturn(SortOrder::SORT_ASC);
        $collectionMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('setCurPage')
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('setPageSize')
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$filterModelMock]));

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);
        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$filterMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Test delete method
     */
    public function testDelete()
    {
        $storeId = 1;
        $filterId = 2;

        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($filterMock)
            ->willReturn(true);

        $this->assertTrue($this->model->delete($filterMock));
    }

    /**
     * Test delete method if specified filter does not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 2
     */
    public function testDeleteException()
    {
        $storeId = 1;
        $filterId = 2;

        $filterOneMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterOneMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $filterTwoMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterTwoMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterTwoMock);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterOneMock, $filterId)
            ->willReturn($filterTwoMock);

        $this->model->delete($filterOneMock);
    }

    /**
     * Test deleteById method
     */
    public function testDeleteById()
    {
        $storeId = 1;
        $filterId = 2;

        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($filterMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($filterId));
    }

    /**
     * Test deleteById method if specified filter does not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 2
     */
    public function testDeleteByIdException()
    {
        $storeId = 1;
        $filterId = 2;

        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->model->deleteById($filterId);
    }
}
