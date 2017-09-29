<?php
namespace Aheadworks\Blog\Test\Unit\Block;

use Aheadworks\Blog\Block\Post;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Source\Post\SharingButtons\DisplayAt;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    const POST_ID = 1;
    const POST_TITLE = 'Post';
    const CATEGORY_NAME = 'Category';

    const DISCUS_FORUM_CODE = 'disqus_forum_code';

    const POST_URL = 'http://localhost/post';
    const CATEGORY_URL = 'http://localhost/cat';

    const CATEGORY_LINK_HTML = '<a href="http://localhost/cat">Category</a>';

    const STORE_ID = 1;

    /**
     * @var array
     */
    private $postCategoryIds = [1, 2];

    /**
     * @var \Aheadworks\Blog\Block\Post
     */
    private $block;

    /**
     * @var \Aheadworks\Blog\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \Magento\Framework\Filter\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filter;

    /**
     * @var \Magento\Framework\View\Element\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    private $childBlock;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $post;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->post = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $this->post->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue(self::POST_TITLE));
        $this->post->expects($this->any())
            ->method('getCategoryIds')
            ->will($this->returnValue($this->postCategoryIds));
        $postRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface');
        $postRepositoryStub->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::POST_ID))
            ->will($this->returnValue($this->post));

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

        $this->category = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $this->category->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::CATEGORY_NAME));
        $categorySearchResultsStub = $this->getMockForAbstractClass(
            'Aheadworks\Blog\Api\Data\CategorySearchResultsInterface'
        );
        $categorySearchResultsStub->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([$this->category]));
        $categoryRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $categoryRepositoryStub->expects($this->any())
            ->method('getList')
            ->with($this->equalTo($searchCriteriaStub))
            ->will($this->returnValue($categorySearchResultsStub));

        $this->config = $this->getMock('Aheadworks\Blog\Model\Config', ['getValue'], [], '', false);

        $urlStub = $this->getMock('Aheadworks\Blog\Model\Url', ['getPostUrl', 'getCategoryUrl'], [], '', false);
        $urlStub->expects($this->any())->method('getPostUrl')
            ->with($this->equalTo($this->post))
            ->will($this->returnValue(self::POST_URL));
        $urlStub->expects($this->any())->method('getCategoryUrl')
            ->with($this->equalTo($this->category))
            ->will($this->returnValue(self::CATEGORY_URL));

        $linkStub = $this->getMock(
            'Aheadworks\Blog\Block\Link',
            [
                'setHref',
                'setTitle',
                'setLabel',
                'toHtml'
            ],
            [],
            '',
            false
        );
        $linkStub->expects($this->any())->method('setHref')->will($this->returnSelf());
        $linkStub->expects($this->any())->method('setTitle')->will($this->returnSelf());
        $linkStub->expects($this->any())->method('setLabel')->will($this->returnSelf());
        $linkStub->expects($this->any())
            ->method('toHtml')
            ->will($this->returnValue(self::CATEGORY_LINK_HTML));
        $linkFactoryStub = $this->getMock('Aheadworks\Blog\Block\LinkFactory', ['create'], [], '', false);
        $linkFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($linkStub));

        $this->filter = $this->getMock('Magento\Framework\Filter\Template', ['setStoreId', 'filter'], [], '', false);
        $this->filter->expects($this->any())
            ->method('setStoreId')
            ->will($this->returnSelf());
        $templateFilterProviderStub = $this->getMock(
            'Aheadworks\Blog\Model\Template\FilterProvider',
            ['getFilter'],
            [],
            '',
            false
        );
        $templateFilterProviderStub->expects($this->any())
            ->method('getFilter')
            ->will($this->returnValue($this->filter));
        $requestStub = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $requestStub->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['post_id', null, self::POST_ID],
                        ['blog_category_id', null, null]
                    ]
                )
            );

        $this->childBlock = $this->getMock(
            'Magento\Framework\View\Element\Template',
            [
                'setTemplate',
                'setShareUrl',
                'setSharingText',
                'setPageIdentifier',
                'setPageUrl',
                'setPageTitle',
                'toHtml'
            ],
            [],
            '',
            false
        );
        $this->childBlock->expects($this->any())->method('setTemplate')->will($this->returnSelf());
        $this->childBlock->expects($this->any())->method('setShareUrl')->will($this->returnSelf());
        $this->childBlock->expects($this->any())->method('setSharingText')->will($this->returnSelf());
        $this->childBlock->expects($this->any())->method('setPageIdentifier')->will($this->returnSelf());
        $this->childBlock->expects($this->any())->method('setPageUrl')->will($this->returnSelf());
        $this->childBlock->expects($this->any())->method('setPageTitle')->will($this->returnSelf());

        $layoutStub = $this->getMockForAbstractClass('Magento\Framework\View\LayoutInterface');
        $layoutStub->expects($this->any())
            ->method('getChildName')
            ->will($this->returnValue('child.name'));
        $layoutStub->expects($this->any())
            ->method('getBlock')
            ->will($this->returnValue($this->childBlock));
        $layoutStub->expects($this->any())
            ->method('createBlock')
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
                'layout' => $layoutStub,
                'storeManager' => $storeManagerStub
            ]
        );

        $this->block = $objectManager->getObject(
            'Aheadworks\Blog\Block\Post',
            [
                'context' => $context,
                'categoryRepository' => $categoryRepositoryStub,
                'postRepository' => $postRepositoryStub,
                'searchCriteriaBuilder' => $searchCriteriaBuilderStub,
                'url' => $urlStub,
                'config' => $this->config,
                'templateFilterProvider' => $templateFilterProviderStub,
                'linkFactory' => $linkFactoryStub
            ]
        );
    }

    /**
     * Testing that a list item mode is checked correctly
     *
     * @dataProvider isListItemModeDataProvider
     */
    public function testIsListItemMode($mode, $expectedResult)
    {
        $this->block->setMode($mode);
        $this->assertEquals($expectedResult, $this->block->isListItemMode());
    }

    /**
     * Testing that a view mode is checked correctly
     *
     * @dataProvider isViewModeDataProvider
     */
    public function testIsViewMode($mode, $expectedResult)
    {
        $this->block->setMode($mode);
        $this->assertEquals($expectedResult, $this->block->isViewMode());
    }

    /**
     * Testing of retrieving of post's categories
     */
    public function testGetCategories()
    {
        $this->assertEquals([$this->category], $this->block->getCategories());
    }

    /**
     * Testing of commentsEnabled method
     *
     * @dataProvider commentsEnabledDataProvider
     */
    public function testCommentsEnabled($disqusForumCode, $isAllowComments, $expectedResult)
    {
        $this->config->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo(Config::XML_GENERAL_DISQUS_FORUM_CODE))
            ->willReturn($disqusForumCode);
        $this->post->expects($this->any())
            ->method('getIsAllowComments')
            ->willReturn($isAllowComments);
        $this->assertEquals($expectedResult, $this->block->commentsEnabled());
    }

    /**
     * Testing of showSharing method
     *
     * @dataProvider showSharingDataProvider
     */
    public function testShowSharing($displaySharingAt, $mode, $expectedResult)
    {
        $this->config->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo(Config::XML_GENERAL_DISPLAY_SHARING_AT))
            ->will($this->returnValue($displaySharingAt));
        $this->block->setMode($mode);
        $this->assertEquals($expectedResult, $this->block->showSharing());
    }

    /**
     * Testing of showReadMoreButton method
     *
     * @dataProvider showReadMoreButtonDataProvider
     */
    public function testShowReadMoreButton($mode, $shortContent, $expectedResult)
    {
        $this->block->setMode($mode);
        $this->post->expects($this->any())
            ->method('getShortContent')
            ->will($this->returnValue($shortContent));
        $this->assertEquals($expectedResult, $this->block->showReadMoreButton($this->post));
    }

    /**
     * Testing of retrieving of sharethis embed html
     */
    public function testGetSharethisEmbedHtml()
    {
        $shareThisHtml = 'sharethis html';
        $this->childBlock->expects($this->any())
            ->method('toHtml')
            ->willReturn($shareThisHtml);
        $this->assertEquals($shareThisHtml, $this->block->getSharethisEmbedHtml());
    }

    /**
     * Testing of retrieving of category link html
     */
    public function testGetCategoryLinkHtml()
    {
        $this->assertEquals(self::CATEGORY_LINK_HTML, $this->block->getCategoryLinkHtml($this->category));
    }

    /**
     * Testing of retrieving of Disqus embed html
     */
    public function testGetDisqusEmbedHtml()
    {
        $disqusEmbedHtml = 'disqus html';
        $this->childBlock->expects($this->any())
            ->method('toHtml')
            ->willReturn($disqusEmbedHtml);
        $this->assertEquals($disqusEmbedHtml, $this->block->getSharethisEmbedHtml());
    }

    /**
     * Testing of getContent method
     *
     * @dataProvider getContentDataProvider
     */
    public function testGetContent($content, $shortContent, $mode, $expectedResult)
    {
        $this->post->expects($this->any())
            ->method('getContent')
            ->willReturn($content);
        $this->post->expects($this->any())
            ->method('getShortContent')
            ->willReturn($shortContent);
        $this->block->setMode($mode);
        $this->filter->expects($this->atLeastOnce())
            ->method('filter')
            ->with($this->equalTo($expectedResult))
            ->willReturn($expectedResult);
        $this->assertEquals($expectedResult, $this->block->getContent($this->post));
    }

    /**
     * @return array
     */
    public function isListItemModeDataProvider()
    {
        return [
            'list item mode' => [Post::MODE_LIST_ITEM, true],
            'view mode' => [Post::MODE_VIEW, false]
        ];
    }

    /**
     * @return array
     */
    public function isViewModeDataProvider()
    {
        return [
            'view mode' => [Post::MODE_VIEW, true],
            'list item mode' => [Post::MODE_LIST_ITEM, false]
        ];
    }

    /**
     * @return array
     */
    public function commentsEnabledDataProvider()
    {
        return [
            'forum code is set, commenting is allowed' => [self::DISCUS_FORUM_CODE, true, true],
            'forum code is not set, commenting is allowed' => [null, true, false],
            'forum code is set, commenting is not allowed' => [self::DISCUS_FORUM_CODE, false, false],
            'forum code is not set, commenting is not allowed' => [null, false, false]
        ];
    }

    /**
     * @return array
     */
    public function showSharingDataProvider()
    {
        return [
            'display at post, view mode' => [DisplayAt::POST, Post::MODE_VIEW, true],
            'display at post, list item mode' => [DisplayAt::POST, Post::MODE_LIST_ITEM, false],
            'display at post list, list item mode' => [DisplayAt::POST_LIST, Post::MODE_LIST_ITEM, true],
            'display at post list, view mode' => [DisplayAt::POST_LIST, Post::MODE_VIEW, false]
        ];
    }

    /**
     * @return array
     */
    public function showReadMoreButtonDataProvider()
    {
        return [
            'list item mode, post has short content' => [Post::MODE_LIST_ITEM, 'short content', true],
            'list item mode, post has not short content' => [Post::MODE_LIST_ITEM, null, false],
            'view mode' => [Post::MODE_VIEW, null, false]
        ];
    }

    /**
     * @return array
     */
    public function getContentDataProvider()
    {
        return [
            'view mode' => ['content', 'short content', Post::MODE_VIEW, 'content'],
            'list item mode, post has short content' => [
                'content',
                'short content',
                Post::MODE_LIST_ITEM,
                'short content'
            ],
            'list item mode, post has not short content' => ['content', null, Post::MODE_LIST_ITEM, 'content']
        ];
    }
}
