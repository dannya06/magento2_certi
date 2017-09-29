<?php
namespace Aheadworks\Blog\Test\Unit\Block\Sidebar;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Sidebar\Recent
 */
class RecentTest extends \PHPUnit_Framework_TestCase
{
    const STORE_ID = 1;
    const RECENT_POSTS_CONFIG_VALUE = 5;

    /**
     * @var \Aheadworks\Blog\Block\Sidebar\Recent
     */
    private $block;

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
            [
                'getData',
                'addFilter',
                'addSortOrder',
                'setPageSize',
                'create'
            ],
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
            ->method('setPageSize')
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
            ->method('getList')
            ->with($this->equalTo($searchCriteriaStub))
            ->will($this->returnValue($searchResultsStub));

        $configStub = $this->getMock('Aheadworks\Blog\Model\Config', ['getValue'], [], '', false);
        $configStub->expects($this->any())
            ->method('getValue')
            ->with(Config::XML_SIDEBAR_RECENT_POSTS)
            ->will($this->returnValue(self::RECENT_POSTS_CONFIG_VALUE));

        $requestStub = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $requestStub->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['post_id', null, null],
                        ['blog_category_id', null, null]
                    ]
                )
            );

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
                'storeManager' => $storeManagerStub
            ]
        );

        $this->block = $objectManager->getObject(
            'Aheadworks\Blog\Block\Sidebar\Recent',
            [
                'context' => $context,
                'postRepository' => $postRepositoryStub,
                'searchCriteriaBuilder' => $searchCriteriaBuilderStub,
                'sortOrderBuilder' => $sortOrderBuilderStub,
                'config' => $configStub
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
}
