<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Aheadworks\Blog\Controller\Router;
use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Url
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    const ROUTE_TO_BLOG = 'blog';
    const POST_URL_KEY = 'post';
    const CATEGORY_URL_KEY = 'cat';
    const TAG_NAME = 'tag';

    /**
     * @var \Aheadworks\Blog\Model\Url
     */
    private $urlModel;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilder;

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

        $configStub = $this->getMockBuilder('Aheadworks\Blog\Model\Config')
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $configStub->expects($this->any())->method('getValue')
            ->with($this->equalTo(Config::XML_GENERAL_ROUTE_TO_BLOG))
            ->will($this->returnValue(self::ROUTE_TO_BLOG));

        $this->urlBuilder = $this->getMockBuilder('Magento\Framework\UrlInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->post = $this->getMockBuilder('Aheadworks\Blog\Api\Data\PostInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->category = $this->getMockBuilder('Aheadworks\Blog\Api\Data\CategoryInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->tag = $this->getMockBuilder('Aheadworks\Blog\Api\Data\TagInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->urlModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Url',
            [
                'config' => $configStub,
                'urlBuilder' => $this->urlBuilder
            ]
        );
    }

    /**
     * Testing that blog home url is built correctly
     */
    public function testGetBlogHomeUrlBuild()
    {
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo(null),
                $this->equalTo(['_direct' => self::ROUTE_TO_BLOG])
            );
        $this->urlModel->getBlogHomeUrl();
    }

    /**
     * Testing return value of 'getBlogHomeUrl' method
     */
    public function testGetBlogHomeUrlResult()
    {
        $blogHomeUrl = 'http://localhost/blog';
        $this->urlBuilder->expects($this->any())
            ->method('getUrl')
            ->willReturn($blogHomeUrl);
        $this->assertEquals($blogHomeUrl, $this->urlModel->getBlogHomeUrl());
    }

    /**
     * Testing that post url is built correctly
     *
     * @dataProvider getPostUrlDataProvider
     */
    public function testGetPostUrlBuild($postUrlKey, $categoryUrlKey, $route)
    {
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->willReturn($postUrlKey);
        if ($categoryUrlKey) {
            $this->category->expects($this->any())
                ->method('getUrlKey')
                ->willReturn($categoryUrlKey);
        }
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo(null),
                $this->equalTo(['_direct' => $route])
            );
        if ($categoryUrlKey) {
            $this->urlModel->getPostUrl($this->post, $this->category);
        } else {
            $this->urlModel->getPostUrl($this->post);
        }
    }

    /**
     * Testing return value of 'getPostUrl' method
     */
    public function testGetPostUrlResult()
    {
        $blogPostUrl = 'http://localhost/blog/post';
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::POST_URL_KEY);
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->willReturn($blogPostUrl);
        $this->assertEquals($blogPostUrl, $this->urlModel->getPostUrl($this->post));
    }

    /**
     * Testing return value of 'getPostRoute' method
     */
    public function testGetPostRouteResult()
    {
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::POST_URL_KEY);
        $this->assertEquals(
            self::ROUTE_TO_BLOG . '/' . self::POST_URL_KEY,
            $this->urlModel->getPostRoute($this->post)
        );
    }

    /**
     * Testing that category url is built correctly
     */
    public function testGetCategoryUrlBuild()
    {
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::CATEGORY_URL_KEY);
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo(null),
                $this->equalTo(['_direct' => self::ROUTE_TO_BLOG . '/' . self::CATEGORY_URL_KEY])
            );
        $this->urlModel->getCategoryUrl($this->category);
    }

    /**
     * Testing return value of 'getCategoryUrl' method
     */
    public function testGetCategoryUrlResult()
    {
        $blogCategoryUrl = 'http://localhost/blog/cat';
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::CATEGORY_URL_KEY);
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->willReturn($blogCategoryUrl);
        $this->assertEquals($blogCategoryUrl, $this->urlModel->getCategoryUrl($this->category));
    }

    /**
     * Testing return value of 'getCategoryRoute' method
     */
    public function testGetCategoryRouteResult()
    {
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::CATEGORY_URL_KEY);
        $this->assertEquals(
            self::ROUTE_TO_BLOG . '/' . self::CATEGORY_URL_KEY,
            $this->urlModel->getCategoryRoute($this->category)
        );
    }

    /**
     * Testing that search by tag url is built correctly
     */
    public function testGetSearchByTagUrlBuild()
    {
        $this->tag->expects($this->any())
            ->method('getName')
            ->willReturn(self::TAG_NAME);
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo(null),
                $this->equalTo(['_direct' => self::ROUTE_TO_BLOG . '/' . Router::TAG_KEY . '/' . self::TAG_NAME])
            );
        $this->urlModel->getSearchByTagUrl($this->tag);
    }

    /**
     * Testing that search by tag name url is built correctly
     */
    public function testGetSearchByTagNameUrlBuild()
    {
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo(null),
                $this->equalTo(['_direct' => self::ROUTE_TO_BLOG . '/' . Router::TAG_KEY . '/' . self::TAG_NAME])
            );
        $this->urlModel->getSearchByTagUrl(self::TAG_NAME);
    }

    /**
     * Testing return value of 'getSearchByTagUrl' method
     */
    public function testGetSearchByTagResult()
    {
        $blogSearchByTagUrl = 'http://localhost/blog/tag/tag';
        $this->tag->expects($this->any())
            ->method('getName')
            ->willReturn(self::TAG_NAME);
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->willReturn($blogSearchByTagUrl);
        $this->assertEquals($blogSearchByTagUrl, $this->urlModel->getSearchByTagUrl($this->tag));
    }

    /**
     * @return array
     */
    public function getPostUrlDataProvider()
    {

        return [
            'post only' => [
                self::POST_URL_KEY,
                null,
                self::ROUTE_TO_BLOG . '/' . self::POST_URL_KEY
            ],
            'post and category' => [
                self::POST_URL_KEY,
                self::CATEGORY_URL_KEY,
                self::ROUTE_TO_BLOG . '/' . self::CATEGORY_URL_KEY . '/' . self::POST_URL_KEY
            ],
        ];
    }
}
