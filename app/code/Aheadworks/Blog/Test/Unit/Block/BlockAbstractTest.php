<?php
namespace Aheadworks\Blog\Test\Unit\Block;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\BlockAbstract
 */
class BlockAbstractTest extends \PHPUnit_Framework_TestCase
{
    const POST_ID = 1;
    const CATEGORY_ID = 2;
    const TAG_NAME = 'tag';

    const POST_URL = 'http://localhost/cat/post';
    const SEARCH_BY_TAG_URL = 'http://localhost/tag/tag';

    /**
     * @var \Aheadworks\Blog\Block\BlockAbstract|\PHPUnit_Framework_MockObject_MockObject
     */
    private $block;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $post;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tag;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->post = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $postRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface');
        $postRepositoryStub->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::POST_ID))
            ->will($this->returnValue($this->post));

        $this->category = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $categoryRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $categoryRepositoryStub->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::CATEGORY_ID))
            ->will($this->returnValue($this->category));

        $this->tag = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $tagRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\TagRepositoryInterface');
        $tagRepositoryStub->expects($this->any())
            ->method('getByName')
            ->will($this->returnValue($this->tag));

        $urlStub = $this->getMock('Aheadworks\Blog\Model\Url', ['getPostUrl', 'getSearchByTagUrl'], [], '', false);
        $urlStub->expects($this->any())
            ->method('getPostUrl')
            ->with(
                $this->equalTo($this->post),
                $this->equalTo($this->category)
            )
            ->will($this->returnValue(self::POST_URL));
        $urlStub->expects($this->any())
            ->method('getSearchByTagUrl')
            ->with($this->equalTo($this->tag))
            ->will($this->returnValue(self::SEARCH_BY_TAG_URL));

        $requestStub = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $requestStub->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['post_id', null, self::POST_ID],
                        ['blog_category_id', null, self::CATEGORY_ID],
                        ['tag', null, self::TAG_NAME]
                    ]
                )
            );
        $context = $objectManager->getObject(
            'Magento\Framework\View\Element\Template\Context',
            ['request' => $requestStub]
        );

        $this->block = $this->getMockForAbstractClass(
            'Aheadworks\Blog\Block\BlockAbstract',
            [
                $context,
                $categoryRepositoryStub,
                $postRepositoryStub,
                $tagRepositoryStub,
                $this->getMock('Magento\Framework\Api\SearchCriteriaBuilder', [], [], '', false),
                $this->getMock('Magento\Framework\Api\FilterBuilder', [], [], '', false),
                $this->getMock('Magento\Framework\Api\SortOrderBuilder', [], [], '', false),
                $urlStub,
                $this->getMock('Aheadworks\Blog\Model\Config', [], [], '', false),
                $this->getMock('Aheadworks\Blog\Model\Template\FilterProvider', [], [], '', false),
                $this->getMock('Aheadworks\Blog\Block\LinkFactory', [], [], '', false)
            ]
        );
    }

    /**
     * Testing of retrieving current post
     */
    public function testGetCurrentPost()
    {
        $this->assertEquals($this->post, $this->block->getCurrentPost());
    }

    /**
     * Testing of retrieving current category
     */
    public function testGetCurrentCategory()
    {
        $this->assertEquals($this->category, $this->block->getCurrentCategory());
    }

    /**
     * Testing of retrieving current tag
     */
    public function testGetCurrentTag()
    {
        $this->assertEquals($this->tag, $this->block->getCurrentTag());
    }

    /**
     * Testing of retrieving post url
     */
    public function testGetPostUrl()
    {
        $this->assertEquals(self::POST_URL, $this->block->getPostUrl($this->post));
    }

    /**
     * Testing of retrieving search by tag url
     */
    public function testGetSearchByTagUrl()
    {
        $this->assertEquals(self::SEARCH_BY_TAG_URL, $this->block->getSearchByTagUrl($this->tag));
    }
}
