<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\PostRepository
 */
class PostRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\PostRepository
     */
    private $postRepository;

    /**
     * @var \Aheadworks\Blog\Model\PostRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postRegistry;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
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
     * @var \Aheadworks\Blog\Model\Post|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postModel;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Post\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postCollection;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $post;

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

        $this->postModel = $this->getMockBuilder('Aheadworks\Blog\Model\Post')
            ->setMethods(['addData', 'save', 'load', 'delete', 'getCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->postModel->expects($this->any())
            ->method('addData')
            ->will($this->returnSelf());
        $this->postModel->expects($this->any())
            ->method('delete')
            ->will($this->returnSelf());
        $postFactoryStab = $this->getMockBuilder('Aheadworks\Blog\Model\PostFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $postFactoryStab->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->postModel));

        $this->postCollection = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Post\Collection')
            ->setMethods(['addFieldToFilter', 'addOrder', 'getSize', 'setCurPage', 'setPageSize', 'getItems'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->postCollection->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $this->postCollection->expects($this->any())
            ->method('addOrder')
            ->will($this->returnSelf());
        $this->postCollection->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue($this->collectionSize));
        $this->postCollection->expects($this->any())
            ->method('setCurPage')
            ->will($this->returnSelf());
        $this->postCollection->expects($this->any())
            ->method('setPageSize')
            ->will($this->returnSelf());
        $this->postCollection->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([$this->postModel]));
        $this->postModel->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($this->postCollection));

        $this->post = $this->getMockBuilder('Aheadworks\Blog\Api\Data\PostInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $postDataFactoryStab = $this->getMockBuilder('Aheadworks\Blog\Api\Data\PostInterfaceFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $postDataFactoryStab->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->post));

        $this->postRegistry = $this->getMockBuilder('Aheadworks\Blog\Model\PostRegistry')
            ->setMethods(['retrieve', 'retrieveByUrlKey', 'remove'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->postRegistry->expects($this->any())
            ->method('retrieve')
            ->will($this->returnValue($this->postModel));
        $this->postRegistry->expects($this->any())
            ->method('retrieveByUrlKey')
            ->will($this->returnValue($this->postModel));

        $this->searchResults = $this->getMockBuilder('Aheadworks\Blog\Api\Data\PostSearchResultsInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $searchResultsFactoryStub = $this->getMockBuilder('Aheadworks\Blog\Api\Data\PostSearchResultsInterfaceFactory')
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
            ->will($this->returnValue($this->postModel->getData()));

        $disqusStub = $this->getMockBuilder('Aheadworks\Blog\Model\Disqus')
            ->disableOriginalConstructor()
            ->getMock();

        $this->postRepository = $objectManager->getObject(
            'Aheadworks\Blog\Model\ResourceModel\PostRepository',
            [
                'postFactory' => $postFactoryStab,
                'postDataFactory' => $postDataFactoryStab,
                'postRegistry' => $this->postRegistry,
                'searchResultsFactory' => $searchResultsFactoryStub,
                'dataObjectHelper' => $this->dataObjectHelper,
                'dataObjectProcessor' => $this->dataObjectProcessorStub,
                'disqus' => $disqusStub
            ]
        );
    }

    /**
     * Testing that the Post Model instance is saved during save method call
     */
    public function testSaveModel()
    {
        $this->postModel->expects($this->once())->method('save');
        $this->postRepository->save($this->post);
    }

    /**
     * Testing that Post data is set
     */
    public function testSaveSetData()
    {
        $this->postModel->expects($this->atLeastOnce())
            ->method('addData')
            ->with($this->equalTo($this->postModel->getData()));
        $this->postRepository->save($this->post);
    }

    /**
     * Testing that Post data is set before saving the model
     */
    public function testSaveSetDataBeforeSaving()
    {
        $this->postModel->expects($this->at(0))
            ->method('addData')
            ->with($this->equalTo($this->postModel->getData()));
        $this->postModel->expects($this->at(1))
            ->method('save');
        $this->postRepository->save($this->post);
    }

    /**
     * Testing load Post Model if post exists during save method call
     */
    public function testSaveLoadExistent()
    {
        $postId = 1;
        $this->post->expects($this->any())
            ->method('getId')
            ->willReturn($postId);
        $this->postModel->expects($this->once())
            ->method('load')
            ->with($this->equalTo($postId))
            ->willReturn($this->returnSelf());
        $this->postRepository->save($this->post);
    }

    /**
     * Testing result of save method
     */
    public function testSaveResult()
    {
        $this->assertSame($this->post, $this->postRepository->save($this->post));
    }

    /**
     * Testing retrieve Post from registry during get method call
     */
    public function testGetRetrieve()
    {
        $postId = 1;
        $this->postRegistry->expects($this->once())
            ->method('retrieve')
            ->with($this->equalTo($postId));
        $this->postRepository->get($postId);
    }

    /**
     * Testing result of get method
     */
    public function testGetResult()
    {
        $postId = 1;
        $this->assertSame($this->post, $this->postRepository->get($postId));
    }

    /**
     * Testing retrieve Post from registry during getByUrlKey method call
     */
    public function testGetByUrlKeyRetrieve()
    {
        $urlKey = 'post';
        $this->postRegistry->expects($this->once())
            ->method('retrieveByUrlKey')
            ->with($this->equalTo($urlKey));
        $this->postRepository->getByUrlKey($urlKey);
    }

    /**
     * Testing result of getByUrlKey method
     */
    public function testGetByUrlKeyResult()
    {
        $urlKey = 'post';
        $this->assertSame($this->post, $this->postRepository->getByUrlKey($urlKey));
    }

    /**
     * Testing that filter added to Post Collection
     */
    public function testGetListAddFilterToCollection()
    {
        $field = PostInterface::TITLE;
        $value = 'Post';
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
        $this->postCollection->expects($this->atLeastOnce())
            ->method('addFieldToFilter')
            ->with($this->equalTo([$field]), $this->equalTo([[$conditionType => $value]]));
        $this->postRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that order added to Post Collection
     */
    public function testGetListAddOrderToCollection()
    {
        $field = PostInterface::TITLE;
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
        $this->postCollection->expects($this->atLeastOnce())
            ->method('addOrder')
            ->with($this->equalTo($field), $this->equalTo($direction));
        $this->postRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that current page is set to Post Collection
     */
    public function testGetListSetCurrPageToCollection()
    {
        $currentPage = 1;
        $this->searchCriteria->expects($this->any())
            ->method('getCurrentPage')
            ->willReturn($currentPage);
        $this->postCollection->expects($this->atLeastOnce())
            ->method('setCurPage')
            ->with($this->equalTo($currentPage));
        $this->postRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that page size is set to Post Collection
     */
    public function testGetListSetPageSizeToCollection()
    {
        $pageSize = 5;
        $this->searchCriteria->expects($this->any())
            ->method('getPageSize')
            ->willReturn($pageSize);
        $this->postCollection->expects($this->atLeastOnce())
            ->method('setPageSize')
            ->with($this->equalTo($pageSize));
        $this->postRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that total items count is sets to result
     */
    public function testGetListSetTotalCountToResult()
    {
        $this->searchResults->expects($this->atLeastOnce())
            ->method('setTotalCount')
            ->with($this->equalTo($this->collectionSize));
        $this->postRepository->getList($this->searchCriteria);
    }

    /**
     * Testing that items is sets to result
     */
    public function testGetListSetItemsToResult()
    {
        $this->searchResults->expects($this->atLeastOnce())
            ->method('setItems')
            ->with($this->equalTo([$this->post]));
        $this->postRepository->getList($this->searchCriteria);
    }

    /**
     * Testing result of getList method
     */
    public function testGetListResult()
    {
        $this->assertSame($this->searchResults, $this->postRepository->getList($this->searchCriteria));
    }

    /**
     * Testing that the Post Model instance is deleted during deleteById method call
     */
    public function testDeleteByIdModelDelete()
    {
        $postId = 1;
        $this->postModel->expects($this->atLeastOnce())->method('delete');
        $this->postRepository->deleteById($postId);
    }

    /**
     * Testing remove Post from registry during deleteById method call
     */
    public function testDeleteByIdRemoveFromRegistry()
    {
        $postId = 1;
        $this->postRegistry->expects($this->atLeastOnce())
            ->method('remove')
            ->with($this->equalTo($postId));
        $this->postRepository->deleteById($postId);
    }
}
