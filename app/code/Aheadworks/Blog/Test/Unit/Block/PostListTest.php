<?php
namespace Aheadworks\Blog\Test\Unit\Block;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\PostList
 */
class PostListTest extends \PHPUnit_Framework_TestCase
{
    const POST_ID = 1;
    const STORE_ID = 1;

    /**
     * @var \Aheadworks\Blog\Block\PostList
     */
    private $block;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layout;

    /**
     * @var \Magento\Framework\View\Element\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    private $childBlock;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $post;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $searchCriteriaStub = $this->getMock('Magento\Framework\Api\SearchCriteria', [], [], '', false);
        $searchCriteriaBuilderStub = $this->getMock(
            'Magento\Framework\Api\SearchCriteriaBuilder',
            ['getData', 'addFilter', 'addSortOrder', 'create'],
            [],
            '',
            false
        );
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('getData')
            ->will($this->returnValue([]));
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('addFilter')
            ->will($this->returnSelf());
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('addSortOrder')
            ->will($this->returnSelf());
        $searchCriteriaBuilderStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($searchCriteriaStub));

        $sortOrderStub = $this->getMock('Magento\Framework\Api\SortOrder');
        $sortOrderBuilderStub = $this->getMock(
            'Magento\Framework\Api\SortOrderBuilder',
            ['setField', 'setDescendingDirection', 'create'],
            [],
            '',
            false
        );
        $sortOrderBuilderStub->expects($this->any())
            ->method('setField')
            ->will($this->returnSelf());
        $sortOrderBuilderStub->expects($this->any())
            ->method('setDescendingDirection')
            ->will($this->returnSelf());
        $sortOrderBuilderStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($sortOrderStub));

        $this->post = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $searchResultsStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostSearchResultsInterface');
        $searchResultsStub->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([$this->post]));
        $postRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface');
        $postRepositoryStub->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::POST_ID))
            ->will($this->returnValue($this->post));
        $postRepositoryStub->expects($this->any())
            ->method('getList')
            ->with($this->equalTo($searchCriteriaStub))
            ->will($this->returnValue($searchResultsStub));

        $requestStub = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $requestStub->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['post_id', null, self::POST_ID],
                        ['blog_category_id', null, null],
                        ['tag', null, null]
                    ]
                )
            );

        $this->childBlock = $this->getMock('Magento\Framework\View\Element\Template', ['toHtml'], [], '', false);
        $this->layout = $this->getMockForAbstractClass('Magento\Framework\View\LayoutInterface');
        $this->layout->expects($this->any())
            ->method('getBlock')
            ->will($this->returnValue($this->childBlock));
        $storeStub = $this->getMockForAbstractClass('Magento\Store\Api\Data\StoreInterface');
        $storeStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::STORE_ID));
        $storeManagerStub = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $storeManagerStub->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeStub));

        $context = $objectManager->getObject(
            'Magento\Framework\View\Element\Template\Context',
            [
                'request' => $requestStub,
                'layout' => $this->layout,
                'storeManager' => $storeManagerStub
            ]
        );

        $this->block = $objectManager->getObject(
            'Aheadworks\Blog\Block\PostList',
            [
                'context' => $context,
                'postRepository' => $postRepositoryStub,
                'searchCriteriaBuilder' => $searchCriteriaBuilderStub,
                'sortOrderBuilder' => $sortOrderBuilderStub
            ]
        );
    }

    /**
     * Testing of retrieving of posts
     */
    public function testGetPosts()
    {
        $this->assertEquals([$this->post], $this->block->getPosts());
    }

    /**
     * testing of getItemHtml method
     */
    public function testGetItemHtml()
    {
        $itemHtml = 'item html';
        $this->layout->expects($this->once())
            ->method('createBlock')
            ->with(
                $this->equalTo('Aheadworks\Blog\Block\Post'),
                $this->anything(),
                $this->contains(['post' => $this->post, 'mode' => \Aheadworks\Blog\Block\Post::MODE_LIST_ITEM])
            )
            ->willReturn($this->childBlock);
        $this->childBlock->expects($this->any())
            ->method('toHtml')
            ->willReturn($itemHtml);
        $this->assertEquals($itemHtml, $this->block->getItemHtml($this->post));
    }

    /**
     * testing of getPagerHtml method
     */
    public function testGetPagerHtml()
    {
        $pagerAlias = 'pager';
        $pagerHtml = 'pager html';
        $this->layout->expects($this->any())
            ->method('getChildName')
            ->with(
                $this->anything(),
                $this->equalTo($pagerAlias)
            )
            ->willReturn($pagerAlias);
        $this->layout->expects($this->any())
            ->method('renderElement')
            ->with($this->equalTo($pagerAlias))
            ->willReturn($pagerHtml);
        $this->assertEquals($pagerHtml, $this->block->getPagerHtml());
    }
}
