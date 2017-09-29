<?php
namespace Aheadworks\Blog\Test\Unit\Ui\Component\Post\Listing\Column;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Post\Listing\Column\Categories
 */
class CategoriesTest extends \PHPUnit_Framework_TestCase
{
    const CATEGORY1_NAME = 'Category 1';
    const CATEGORY2_NAME = 'Category 2';
    const POST_CATEGORY_IDS = [1, 2];

    /**
     * @var \Aheadworks\Blog\Ui\Component\Post\Listing\Column\Categories
     */
    private $column;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category1;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category2;

    /**
     * @var array
     */
    private $post = [
        'category_ids' => self::POST_CATEGORY_IDS
    ];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $processorStub = $this->getMock(
            'Magento\Framework\View\Element\UiComponent\Processor',
            ['register'],
            [],
            '',
            false
        );
        $contextStub = $this->getMockForAbstractClass('Magento\Framework\View\Element\UiComponent\ContextInterface');
        $contextStub->expects($this->any())
            ->method('getProcessor')
            ->will($this->returnValue($processorStub));

        $this->category1 = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $this->category1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::CATEGORY1_NAME));
        $this->category2 = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $this->category2->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::CATEGORY2_NAME));

        $searchCriteriaStub = $this->getMock('Magento\Framework\Api\SearchCriteria', [], [], '', false);
        $searchCriteriaBuilderStub = $this->getMock(
            'Magento\Framework\Api\SearchCriteriaBuilder',
            ['addFilter', 'create'],
            [],
            '',
            false
        );
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('addFilter')
            ->will($this->returnSelf());
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($searchCriteriaStub));

        $searchResultsStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategorySearchResultsInterface');
        $searchResultsStub->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([$this->category1, $this->category2]));

        $categoryRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $categoryRepositoryStub->expects($this->any())
            ->method('getList')
            ->with($this->equalTo($searchCriteriaStub))
            ->will($this->returnValue($searchResultsStub));

        $this->column = $objectManager->getObject(
            'Aheadworks\Blog\Ui\Component\Post\Listing\Column\Categories',
            [
                'context' => $contextStub,
                'categoryRepository' => $categoryRepositoryStub,
                'searchCriteriaBuilder' => $searchCriteriaBuilderStub
            ]
        );
    }

    /**
     * Testing of prepareDataSource method
     */
    public function testPrepareDataSource()
    {
        $dataSource = ['data' => ['items' => [$this->post]]];
        $dataSourcePrepared = $this->column->prepareDataSource($dataSource);
        $postItem = $dataSourcePrepared['data']['items'][0];
        $this->assertArrayHasKey('categories', $postItem);
        $this->assertEquals(self::CATEGORY1_NAME . ', ' . self::CATEGORY2_NAME, $postItem['categories']);
    }
}
