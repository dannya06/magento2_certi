<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\TagManagement
 */
class TagManagementTest extends \PHPUnit_Framework_TestCase
{
    const COLLECTION_SIZE = 10;
    const CONFIG_SIDEBAR_POPULAR_TAGS = 10;

    const STORE_ID = 1;
    const CATEGORY_ID = 1;

    /**
     * @var \Aheadworks\Blog\Model\TagManagement
     */
    private $tagManagement;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagCollection;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResults;

    /**
     * @var \Aheadworks\Blog\Model\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagModel;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tag;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->tagModel = $this->getMockBuilder('Aheadworks\Blog\Model\Tag')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagModel->expects($this->any())
            ->method('getData')
            ->will($this->returnValue([]));

        $this->tagCollection = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Tag\Collection')
            ->setMethods(['joinCount', 'addCategoryFilter', 'getIterator', 'getSize', 'addOrder', 'setPageSize'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagCollection->expects($this->any())
            ->method('joinCount')
            ->will($this->returnSelf());
        $this->tagCollection->expects($this->any())
            ->method('addCategoryFilter')
            ->will($this->returnSelf());
        $this->tagCollection->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$this->tagModel])));
        $this->tagCollection->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(self::COLLECTION_SIZE));
        $this->tagCollection->expects($this->any())
            ->method('addOrder')
            ->will($this->returnSelf());
        $this->tagCollection->expects($this->any())
            ->method('setPageSize')
            ->will($this->returnSelf());
        $collectionFactoryStub = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Tag\CollectionFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $collectionFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->tagCollection));

        $configStub = $this->getMockBuilder('Aheadworks\Blog\Model\Config')
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $configStub->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo(Config::XML_SIDEBAR_POPULAR_TAGS))
            ->will($this->returnValue(self::CONFIG_SIDEBAR_POPULAR_TAGS));

        $this->tag = $this->getMockBuilder('Aheadworks\Blog\Api\Data\TagInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $tagDataFactoryStub = $this->getMockBuilder('Aheadworks\Blog\Api\Data\TagInterfaceFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagDataFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->tag));

        $this->searchResults = $this->getMockBuilder('Aheadworks\Blog\Api\Data\TagSearchResultsInterface')
            ->setMethods(['setItems', 'setTotalCount'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->searchResults->expects($this->any())
            ->method('setItems')
            ->will($this->returnSelf());
        $this->searchResults->expects($this->any())
            ->method('setTotalCount')
            ->will($this->returnSelf());
        $searchResultsFactoryStub = $this->getMockBuilder('Aheadworks\Blog\Api\Data\TagSearchResultsInterfaceFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchResultsFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->searchResults));

        $dataObjectHelper = $this->getMockBuilder('Magento\Framework\Api\DataObjectHelper')
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->tagManagement = $objectManager->getObject(
            'Aheadworks\Blog\Model\TagManagement',
            [
                'collectionFactory' => $collectionFactoryStub,
                'config' => $configStub,
                'tagDataFactory' => $tagDataFactoryStub,
                'searchResultsFactory' => $searchResultsFactoryStub,
                'dataObjectHelper' => $dataObjectHelper
            ]
        );
    }

    /**
     * Testing that tag counts are joined to collection for given store ID
     */
    public function testGetCloudTagsJoinCounts()
    {
        $this->tagCollection->expects($this->atLeastOnce())
            ->method('joinCount')
            ->with($this->equalTo(self::STORE_ID));
        $this->tagManagement->getCloudTags(self::STORE_ID);
    }

    /**
     * Testing that collection page size is equals to config value
     */
    public function testGetCloudTagsSetPageSize()
    {
        $this->tagCollection->expects($this->atLeastOnce())
            ->method('setPageSize')
            ->with($this->equalTo(self::CONFIG_SIDEBAR_POPULAR_TAGS));
        $this->tagManagement->getCloudTags(self::STORE_ID);
    }

    /**
     * Testing that category filter is not added to collection by default
     */
    public function testGetCloudTagsCategoryFilterNotAdded()
    {
        $this->tagCollection->expects($this->never())
            ->method('addCategoryFilter');
        $this->tagManagement->getCloudTags(self::STORE_ID);
    }

    /**
     * Testing that category filter is not added to collection when filtered by category
     */
    public function testGetCloudTagsCategoryFilterAdded()
    {
        $this->tagCollection->expects($this->atLeastOnce())
            ->method('addCategoryFilter');
        $this->tagManagement->getCloudTags(self::STORE_ID, self::CATEGORY_ID);
    }

    /**
     * Testing that total items count is sets to result
     */
    public function testGetCloudTagsSetTotalCountToResult()
    {
        $this->searchResults->expects($this->atLeastOnce())
            ->method('setTotalCount')
            ->with($this->equalTo(self::COLLECTION_SIZE));
        $this->tagManagement->getCloudTags(self::STORE_ID);
    }

    /**
     * Testing that items is sets to result
     */
    public function testGetCloudTagsSetItemsToResult()
    {
        $this->searchResults->expects($this->atLeastOnce())
            ->method('setItems')
            ->with($this->equalTo([$this->tag]));
        $this->tagManagement->getCloudTags(self::STORE_ID);
    }

    /**
     * Testing return value of getCloudTags method
     */
    public function testGetCloudTagsResult()
    {
        $this->assertSame($this->searchResults, $this->tagManagement->getCloudTags(self::STORE_ID));
    }
}
