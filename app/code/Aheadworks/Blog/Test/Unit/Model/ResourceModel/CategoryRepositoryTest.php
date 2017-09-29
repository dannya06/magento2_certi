<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\CategoryRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \Aheadworks\Blog\Model\CategoryRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRegistry;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategorySearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResults;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteria;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroup|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterGroup;

    /**
     * @var \Magento\Framework\Api\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filter;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorStub;

    /**
     * @var \Aheadworks\Blog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryModel;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryCollection;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    /**
     * @var int
     */
    private $collectionSize = 10;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->categoryModel = $this->getMockBuilder('Aheadworks\Blog\Model\Category')
            ->setMethods(['addData', 'save', 'load', 'delete', 'getCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryModel->expects($this->any())
            ->method('addData')
            ->will($this->returnSelf());
        $this->categoryModel->expects($this->any())
            ->method('delete')
            ->will($this->returnSelf());
        $categoryFactoryStab = $this->getMockBuilder('Aheadworks\Blog\Model\CategoryFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryFactoryStab->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->categoryModel));

        $this->categoryCollection = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Category\Collection')
            ->setMethods(['addFieldToFilter', 'addOrder', 'getSize', 'setCurPage', 'setPageSize', 'getIterator'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryCollection->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $this->categoryCollection->expects($this->any())
            ->method('addOrder')
            ->will($this->returnSelf());
        $this->categoryCollection->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue($this->collectionSize));
        $this->categoryCollection->expects($this->any())
            ->method('setCurPage')
            ->will($this->returnSelf());
        $this->categoryCollection->expects($this->any())
            ->method('setPageSize')
            ->will($this->returnSelf());
        $this->categoryCollection->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$this->categoryModel])));
        $this->categoryModel->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($this->categoryCollection));

        $this->category = $this->getMockBuilder('Aheadworks\Blog\Api\Data\CategoryInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $categoryDataFactoryStab = $this->getMockBuilder('Aheadworks\Blog\Api\Data\CategoryInterfaceFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryDataFactoryStab->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->category));

        $this->categoryRegistry = $this->getMockBuilder('Aheadworks\Blog\Model\CategoryRegistry')
            ->setMethods(['retrieve', 'retrieveByUrlKey', 'remove'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryRegistry->expects($this->any())
            ->method('retrieve')
            ->will($this->returnValue($this->categoryModel));
        $this->categoryRegistry->expects($this->any())
            ->method('retrieveByUrlKey')
            ->will($this->returnValue($this->categoryModel));

        $this->searchResults = $this->getMockBuilder('Aheadworks\Blog\Api\Data\CategorySearchResultsInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $searchResultsFactoryStub = $this->getMockBuilder(
            'Aheadworks\Blog\Api\Data\CategorySearchResultsInterfaceFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchResultsFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->searchResults));

        $this->searchCriteria = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->searchResults->expects($this->any())
            ->method('setSearchCriteria')
            ->will($this->returnSelf());

        $this->filterGroup = $this->getMockBuilder('Magento\Framework\Api\Search\FilterGroup')
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteria->expects($this->any())
            ->method('getFilterGroups')
            ->will($this->returnValue([$this->filterGroup]));

        $this->filter = $this->getMockBuilder('Magento\Framework\Api\Filter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterGroup->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue([$this->filter]));

        $this->dataObjectHelper = $this->getMockBuilder('Magento\Framework\Api\DataObjectHelper')
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataObjectProcessorStub = $this->getMockBuilder('Magento\Framework\Reflection\DataObjectProcessor')
            ->setMethods(['buildOutputDataArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectProcessorStub->expects($this->any())
            ->method('buildOutputDataArray')
            ->will($this->returnValue($this->categoryModel->getData()));

        $this->categoryRepository = $objectManager->getObject(
            'Aheadworks\Blog\Model\ResourceModel\CategoryRepository',
            [
                'categoryFactory' => $categoryFactoryStab,
                'categoryDataFactory' => $categoryDataFactoryStab,
                'categoryRegistry' => $this->categoryRegistry,
                'searchResultsFactory' => $searchResultsFactoryStub,
                'dataObjectHelper' => $this->dataObjectHelper,
                'dataObjectProcessor' => $this->dataObjectProcessorStub
            ]
        );
    }

    /**
     * Testing that the Category Model instance is saved during save method call
     */
    public function testSaveModel()
    {
        $this->categoryModel->expects($this->once())->method('save');
        $this->categoryRepository->save($this->category);
    }

    /**
     * Testing that Category data is set
     */
    public function testSaveSetData()
    {
        $this->categoryModel->expects($this->atLeastOnce())
            ->method('addData')
            ->with($this->equalTo($this->categoryModel->getData()));
        $this->categoryRepository->save($this->category);
    }

    /**
     * Testing that Category data is set before saving the model
     */
    public function testSaveSetDataBeforeSaving()
    {
        $this->categoryModel->expects($this->at(0))
            ->method('addData')
            ->with($this->equalTo($this->categoryModel->getData()));
        $this->categoryModel->expects($this->at(1))
            ->method('save');
        $this->categoryRepository->save($this->category);
    }

    /**
     * Testing load Category Model if category exists during save method call
     */
    public function testSaveLoadExistent()
    {
        $categoryId = 1;
        $this->category->expects($this->any())
            ->method('getId')
            ->willReturn($categoryId);
        $this->categoryModel->expects($this->once())
            ->method('load')
            ->with($this->equalTo($categoryId))
            ->willReturn($this->returnSelf());
        $this->categoryRepository->save($this->category);
    }

    /**
     * Testing result of save method
     */
    public function testSaveResult()
    {
        $this->assertSame($this->category, $this->categoryRepository->save($this->category));
    }

    /**
     * Testing retrieve Category from registry during get method call
     */
    public function testGetRetrieve()
    {
        $categoryId = 1;
        $this->categoryRegistry->expects($this->once())
            ->method('retrieve')
            ->with($this->equalTo($categoryId));
        $this->categoryRepository->get($categoryId);
    }

    /**
     * Testing result of get method
     */
    public function testGetResult()
    {
        $categoryId = 1;
        $this->assertSame($this->category, $this->categoryRepository->get($categoryId));
    }

    /**
     * Testing retrieve Category from registry during getByUrlKey method call
     */
    public function testGetByUrlKeyRetrieve()
    {
        $urlKey = 'category';
        $this->categoryRegistry->expects($this->once())
            ->method('retrieveByUrlKey')
            ->with($this->equalTo($urlKey));
        $this->categoryRepository->getByUrlKey($urlKey);
    }

    /**
     * Testing result of getByUrlKey method
     */
    public function testGetByUrlKeyResult()
    {
        $urlKey = 'category';
        $this->assertSame($this->category, $this->categoryRepository->getByUrlKey($urlKey));
    }

    /**
     * Testing that filter added to Category Collection
     */
    public function testGetListAddFilterToCollection()
    {
        $field = CategoryInterface::NAME;
        $value = 'Category';
        $conditionType = 'eq';
        $this->filter->expects($this->any())
            ->method('getField')
            ->willReturn($field);
        $this->filter->expects($this->any())
            ->method('getValue')
            ->willReturn($value);
        $this->filter->expects($this->any())
            ->method('getConditionType')
            ->willReturn($conditionType);
        $this->categoryCollection->expects($this->atLeastOnce())
            ->method('addFieldToFilter')
            ->with($this->equalTo([$field]), $this->equalTo([[$conditionType => $value]]));
        $this->categoryRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that order added to Category Collection
     */
    public function testGetListAddOrderToCollection()
    {
        $field = CategoryInterface::NAME;
        $direction = 'ASC';
        $sortOrder = $this->getMockBuilder('Magento\Framework\Api\SortOrder')
            ->setMethods(['getField', 'getDirection'])
            ->disableOriginalConstructor()
            ->getMock();
        $sortOrder->expects($this->any())
            ->method('getField')
            ->willReturn($field);
        $sortOrder->expects($this->any())
            ->method('getDirection')
            ->willReturn($direction);
        $this->searchCriteria->expects($this->any())
            ->method('getSortOrders')
            ->willReturn([$sortOrder]);
        $this->categoryCollection->expects($this->atLeastOnce())
            ->method('addOrder')
            ->with($this->equalTo($field), $this->equalTo($direction));
        $this->categoryRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that current page is set to Category Collection
     */
    public function testGetListSetCurrPageToCollection()
    {
        $currentPage = 1;
        $this->searchCriteria->expects($this->any())
            ->method('getCurrentPage')
            ->willReturn($currentPage);
        $this->categoryCollection->expects($this->atLeastOnce())
            ->method('setCurPage')
            ->with($this->equalTo($currentPage));
        $this->categoryRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that page size is set to Category Collection
     */
    public function testGetListSetPageSizeToCollection()
    {
        $pageSize = 5;
        $this->searchCriteria->expects($this->any())
            ->method('getPageSize')
            ->willReturn($pageSize);
        $this->categoryCollection->expects($this->atLeastOnce())
            ->method('setPageSize')
            ->with($this->equalTo($pageSize));
        $this->categoryRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that total items count is sets to result
     */
    public function testGetListSetTotalCountToResult()
    {
        $this->searchResults->expects($this->atLeastOnce())
            ->method('setTotalCount')
            ->with($this->equalTo($this->collectionSize));
        $this->categoryRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that items is sets to result
     */
    public function testGetListSetItemsToResult()
    {
        $this->searchResults->expects($this->atLeastOnce())
            ->method('setItems')
            ->with($this->equalTo([$this->category]));
        $this->categoryRepository->getList($this->searchCriteria);
    }

    /**
     * Testing result of getList method
     */
    public function testGetListResult()
    {
        $this->assertSame($this->searchResults, $this->categoryRepository->getList($this->searchCriteria));
    }

    /**
     * Testing that the Category Model instance is deleted during deleteById method call
     */
    public function testDeleteByIdModelDelete()
    {
        $categoryId = 1;
        $this->categoryModel->expects($this->atLeastOnce())->method('delete');
        $this->categoryRepository->deleteById($categoryId);
    }

    /**
     * Testing remove Category from registry during deleteById method call
     */
    public function testDeleteByIdRemoveFromRegistry()
    {
        $categoryId = 1;
        $this->categoryRegistry->expects($this->atLeastOnce())
            ->method('remove')
            ->with($this->equalTo($categoryId));
        $this->categoryRepository->deleteById($categoryId);
    }
}
