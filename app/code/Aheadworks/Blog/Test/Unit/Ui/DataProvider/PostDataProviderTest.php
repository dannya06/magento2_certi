<?php
namespace Aheadworks\Blog\Test\Unit\Ui\DataProvider;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\DataProvider\PostDataProvider
 */
class PostDataProviderTest extends \PHPUnit_Framework_TestCase
{
    const DATA_PROVIDER_NAME = 'post_listing_data_source';
    const PRIMARY_FIELD_NAME = 'post_id';
    const REQUEST_FIELD_NAME = 'post_id';

    const POST_ID = 1;
    const POST_STORE_IDS = [1, 2];

    const TOTAL_COUNT = 1;

    /**
     * @var \Aheadworks\Blog\Ui\DataProvider\PostDataProvider
     */
    private $dataProvider;

    /**
     * @var \Aheadworks\Blog\Api\PostRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postRepository;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResult;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessor;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $post;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->post = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getStoreIds')
            ->will($this->returnValue(self::POST_STORE_IDS));

        $searchCriteriaStub = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchResult = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostSearchResultsInterface');
        $this->searchResult->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([$this->post]));
        $this->searchResult->expects($this->any())
            ->method('getTotalCount')
            ->will($this->returnValue(self::TOTAL_COUNT));

        $this->postRepository = $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface');
        $this->postRepository->expects($this->any())
            ->method('getList')
            ->with($this->equalTo($searchCriteriaStub))
            ->will($this->returnValue($this->searchResult));

        $searchCriteriaBuilderStub = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaBuilder')
            ->setMethods(
                [
                    'addFilters',
                    'setSortOrders',
                    'setPageSize',
                    'setCurrentPage',
                    'create'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('addFilters')
            ->will($this->returnSelf());
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('setSortOrders')
            ->will($this->returnSelf());
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('setPageSize')
            ->will($this->returnSelf());
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('setCurrentPage')
            ->will($this->returnSelf());
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($searchCriteriaStub));

        $this->dataObjectProcessor = $this->getMockBuilder('Magento\Framework\Reflection\DataObjectProcessor')
            ->setMethods(['buildOutputDataArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');

        $this->dataProvider = $objectManager->getObject(
            'Aheadworks\Blog\Ui\DataProvider\PostDataProvider',
            [
                'name' => self::DATA_PROVIDER_NAME,
                'primaryFieldName' => self::PRIMARY_FIELD_NAME,
                'requestFieldName' => self::REQUEST_FIELD_NAME,
                'repository' => $this->postRepository,
                'searchCriteriaBuilder' => $searchCriteriaBuilderStub,
                'dataObjectProcessor' => $this->dataObjectProcessor,
                'request' => $this->request
            ]
        );
    }

    /**
     * Testing return value of getSearchResult method
     */
    public function testGetSearchResult()
    {
        $this->assertEquals($this->searchResult, $this->dataProvider->getSearchResult());
    }

    /**
     * Testing that the items are retrieved from search result
     */
    public function testGetDataGetItems()
    {
        $this->searchResult->expects($this->atLeastOnce())->method('getItems');
        $this->dataProvider->getData();
    }

    /**
     * Testing that the items are converted to array
     */
    public function testGetDataConvertToArray()
    {
        $this->dataObjectProcessor->expects($this->atLeastOnce())->method('buildOutputDataArray');
        $this->dataProvider->getData();
    }

    /**
     * Testing return value of getData method for listing
     */
    public function testGetDataResultForListing()
    {
        $itemData = [
            'id' => self::POST_ID,
            'store_ids' => self::POST_STORE_IDS
        ];
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo(self::REQUEST_FIELD_NAME))
            ->willReturn(null);
        $this->dataObjectProcessor->expects($this->any())
            ->method('buildOutputDataArray')
            ->with(
                $this->equalTo($this->post),
                $this->equalTo('Aheadworks\Blog\Api\Data\PostInterface')
            )
            ->will($this->returnValue($itemData));
        $this->assertEquals(
            [
                'totalRecords' => self::TOTAL_COUNT,
                'items' => [array_merge($itemData, ['store_id' => self::POST_STORE_IDS])]
            ],
            $this->dataProvider->getData()
        );
    }

    /**
     * Testing return value of getData method for form
     */
    public function testGetDataResultForForm()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo(self::REQUEST_FIELD_NAME))
            ->willReturn(self::POST_ID);
        $this->dataObjectProcessor->expects($this->any())
            ->method('buildOutputDataArray')
            ->with(
                $this->equalTo($this->post),
                $this->equalTo('Aheadworks\Blog\Api\Data\PostInterface')
            )
            ->will(
                $this->returnValue(
                    [
                        'id' => self::POST_ID,
                        'store_ids' => self::POST_STORE_IDS,
                        'virtual_status' => 'draft',
                        'is_allow_comments' => true,
                        'tags' => ['tag1', 'tag2'],
                        'category_ids' => [1, 2]
                    ]
                )
            );
        $data = $this->dataProvider->getData();
        $this->assertArrayHasKey(self::POST_ID, $data);
        $this->assertArrayHasKey('post', $data[self::POST_ID]);
    }
}
