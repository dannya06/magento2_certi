<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\TagInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\TagRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TagRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\TagRepository
     */
    private $tagRepository;

    /**
     * @var \Aheadworks\Blog\Model\TagRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagRegistry;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
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
     * @var \Aheadworks\Blog\Model\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagModel;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagCollection;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tag;

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

        $this->tagModel = $this->getMockBuilder('Aheadworks\Blog\Model\Tag')
            ->setMethods(['addData', 'save', 'load', 'delete', 'getCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagModel->expects($this->any())
            ->method('addData')
            ->will($this->returnSelf());
        $this->tagModel->expects($this->any())
            ->method('delete')
            ->will($this->returnSelf());
        $tagFactoryStab = $this->getMockBuilder('Aheadworks\Blog\Model\TagFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagFactoryStab->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->tagModel));

        $this->tagCollection = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Tag\Collection')
            ->setMethods(['addFieldToFilter', 'addOrder', 'getSize', 'setCurPage', 'setPageSize', 'getIterator'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagCollection->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $this->tagCollection->expects($this->any())
            ->method('addOrder')
            ->will($this->returnSelf());
        $this->tagCollection->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue($this->collectionSize));
        $this->tagCollection->expects($this->any())
            ->method('setCurPage')
            ->will($this->returnSelf());
        $this->tagCollection->expects($this->any())
            ->method('setPageSize')
            ->will($this->returnSelf());
        $this->tagCollection->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$this->tagModel])));
        $this->tagModel->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($this->tagCollection));

        $this->tag = $this->getMockBuilder('Aheadworks\Blog\Api\Data\TagInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $tagDataFactoryStab = $this->getMockBuilder('Aheadworks\Blog\Api\Data\TagInterfaceFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagDataFactoryStab->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->tag));

        $this->tagRegistry = $this->getMockBuilder('Aheadworks\Blog\Model\TagRegistry')
            ->setMethods(['retrieve', 'retrieveByName', 'remove'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagRegistry->expects($this->any())
            ->method('retrieve')
            ->will($this->returnValue($this->tagModel));
        $this->tagRegistry->expects($this->any())
            ->method('retrieveByName')
            ->will($this->returnValue($this->tagModel));

        $this->searchResults = $this->getMockBuilder('Aheadworks\Blog\Api\Data\TagSearchResultsInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $searchResultsFactoryStub = $this->getMockBuilder('Aheadworks\Blog\Api\Data\TagSearchResultsInterfaceFactory')
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
            ->will($this->returnValue($this->tagModel->getData()));

        $this->tagRepository = $objectManager->getObject(
            'Aheadworks\Blog\Model\ResourceModel\TagRepository',
            [
                'tagFactory' => $tagFactoryStab,
                'tagDataFactory' => $tagDataFactoryStab,
                'tagRegistry' => $this->tagRegistry,
                'searchResultsFactory' => $searchResultsFactoryStub,
                'dataObjectHelper' => $this->dataObjectHelper,
                'dataObjectProcessor' => $this->dataObjectProcessorStub
            ]
        );
    }

    /**
     * Testing that the tag Model instance is saved during save method call
     */
    public function testSaveModel()
    {
        $this->tagModel->expects($this->once())->method('save');
        $this->tagRepository->save($this->tag);
    }

    /**
     * Testing that Tag data is set
     */
    public function testSaveSetData()
    {
        $this->tagModel->expects($this->atLeastOnce())
            ->method('addData')
            ->with($this->equalTo($this->tagModel->getData()));
        $this->tagRepository->save($this->tag);
    }

    /**
     * Testing that Tag data is set before saving the model
     */
    public function testSaveSetDataBeforeSaving()
    {
        $this->tagModel->expects($this->at(0))
            ->method('addData')
            ->with($this->equalTo($this->tagModel->getData()));
        $this->tagModel->expects($this->at(1))
            ->method('save');
        $this->tagRepository->save($this->tag);
    }

    /**
     * Testing load Tag Model if tag exists during save method call
     */
    public function testSaveLoadExistent()
    {
        $tagId = 1;
        $this->tag->expects($this->any())
            ->method('getId')
            ->willReturn($tagId);
        $this->tagModel->expects($this->once())
            ->method('load')
            ->with($this->equalTo($tagId))
            ->willReturn($this->returnSelf());
        $this->tagRepository->save($this->tag);
    }

    /**
     * Testing result of save method
     */
    public function testSaveResult()
    {
        $this->assertSame($this->tag, $this->tagRepository->save($this->tag));
    }

    /**
     * Testing retrieve Tag from registry during get method call
     */
    public function testGetRetrieve()
    {
        $tagId = 1;
        $this->tagRegistry->expects($this->once())
            ->method('retrieve')
            ->with($this->equalTo($tagId));
        $this->tagRepository->get($tagId);
    }

    /**
     * Testing result of get method
     */
    public function testGetResult()
    {
        $tagId = 1;
        $this->assertSame($this->tag, $this->tagRepository->get($tagId));
    }

    /**
     * Testing retrieve Tag from registry during getByName method call
     */
    public function testGetByNameKeyRetrieve()
    {
        $name = 'Tag';
        $this->tagRegistry->expects($this->once())
            ->method('retrieveByName')
            ->with($this->equalTo($name));
        $this->tagRepository->getByName($name);
    }

    /**
     * Testing result of getByName method
     */
    public function testGetByNameKeyResult()
    {
        $name = 'Tag';
        $this->assertSame($this->tag, $this->tagRepository->getByName($name));
    }

    /**
     * Testing that filter added to Tag Collection
     */
    public function testGetListAddFilterToCollection()
    {
        $field = TagInterface::NAME;
        $value = 'Tag';
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
        $this->tagCollection->expects($this->atLeastOnce())
            ->method('addFieldToFilter')
            ->with($this->equalTo([$field]), $this->equalTo([[$conditionType => $value]]));
        $this->tagRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that order added to Tag Collection
     */
    public function testGetListAddOrderToCollection()
    {
        $field = TagInterface::NAME;
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
        $this->tagCollection->expects($this->atLeastOnce())
            ->method('addOrder')
            ->with($this->equalTo($field), $this->equalTo($direction));
        $this->tagRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that current page is set to Tag Collection
     */
    public function testGetListSetCurrPageToCollection()
    {
        $currentPage = 1;
        $this->searchCriteria->expects($this->any())
            ->method('getCurrentPage')
            ->willReturn($currentPage);
        $this->tagCollection->expects($this->atLeastOnce())
            ->method('setCurPage')
            ->with($this->equalTo($currentPage));
        $this->tagRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that page size is set to Tag Collection
     */
    public function testGetListSetPageSizeToCollection()
    {
        $pageSize = 5;
        $this->searchCriteria->expects($this->any())
            ->method('getPageSize')
            ->willReturn($pageSize);
        $this->tagCollection->expects($this->atLeastOnce())
            ->method('setPageSize')
            ->with($this->equalTo($pageSize));
        $this->tagRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that total items count is sets to result
     */
    public function testGetListSetTotalCountToResult()
    {
        $this->searchResults->expects($this->atLeastOnce())
            ->method('setTotalCount')
            ->with($this->equalTo($this->collectionSize));
        $this->tagRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that items is sets to result
     */
    public function testGetListSetItemsToResult()
    {
        $this->searchResults->expects($this->atLeastOnce())
            ->method('setItems')
            ->with($this->equalTo([$this->tag]));
        $this->tagRepository->getList($this->searchCriteria);
    }

    /**
     * Testing result of getList method
     */
    public function testGetListResult()
    {
        $this->assertSame($this->searchResults, $this->tagRepository->getList($this->searchCriteria));
    }

    /**
     * Testing that the Tag Model instance is deleted during deleteById method call
     */
    public function testDeleteByIdModelDelete()
    {
        $tagId = 1;
        $this->tagModel->expects($this->atLeastOnce())->method('delete');
        $this->tagRepository->deleteById($tagId);
    }

    /**
     * Testing remove Tag from registry during deleteById method call
     */
    public function testDeleteByIdRemoveFromRegistry()
    {
        $tagId = 1;
        $this->tagRegistry->expects($this->atLeastOnce())
            ->method('remove')
            ->with($this->equalTo($tagId));
        $this->tagRepository->deleteById($tagId);
    }
}
