<?php
namespace Aheadworks\Blog\Test\Unit\Ui\DataProvider;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\DataProvider\CategoryDataProvider
 */
class CategoryDataProviderTest extends \PHPUnit_Framework_TestCase
{
    const DATA_PROVIDER_NAME = 'category_listing_data_source';
    const PRIMARY_FIELD_NAME = 'cat_id';
    const REQUEST_FIELD_NAME = 'cat_id';

    const CATEGORY_ID = 1;
    const CATEGORY_STORE_IDS = [1, 2];

    const TOTAL_COUNT = 1;

    /**
     * @var \Aheadworks\Blog\Ui\DataProvider\CategoryDataProvider
     */
    private $dataProvider;

    /**
     * @var \Aheadworks\Blog\Api\CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepository;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategorySearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResult;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessor;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->category = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getStoreIds')
            ->will($this->returnValue(self::CATEGORY_STORE_IDS));

        $searchCriteriaStub = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchResult = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategorySearchResultsInterface');
        $this->searchResult->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([$this->category]));
        $this->searchResult->expects($this->any())
            ->method('getTotalCount')
            ->will($this->returnValue(self::TOTAL_COUNT));

        $this->categoryRepository = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $this->categoryRepository->expects($this->any())
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

        $this->dataProvider = $objectManager->getObject(
            'Aheadworks\Blog\Ui\DataProvider\CategoryDataProvider',
            [
                'name' => self::DATA_PROVIDER_NAME,
                'primaryFieldName' => self::PRIMARY_FIELD_NAME,
                'requestFieldName' => self::REQUEST_FIELD_NAME,
                'repository' => $this->categoryRepository,
                'searchCriteriaBuilder' => $searchCriteriaBuilderStub,
                'dataObjectProcessor' => $this->dataObjectProcessor
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
     * Testing return value of getData method
     */
    public function testGetDataResult()
    {
        $itemData = [
            'id' => self::CATEGORY_ID,
            'store_ids' => self::CATEGORY_STORE_IDS
        ];
        $this->dataObjectProcessor->expects($this->any())
            ->method('buildOutputDataArray')
            ->with(
                $this->equalTo($this->category),
                $this->equalTo('Aheadworks\Blog\Api\Data\CategoryInterface')
            )
            ->will($this->returnValue($itemData));
        $this->assertEquals(
            [
                'totalRecords' => self::TOTAL_COUNT,
                'items' => [array_merge($itemData, ['store_id' => self::CATEGORY_STORE_IDS])]
            ],
            $this->dataProvider->getData()
        );
    }
}
