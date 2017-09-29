<?php
namespace Aheadworks\Blog\Test\Unit\Controller;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Router
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    const CATEGORY_ID = 1;
    const CATEGORY_URL_KEY = 'cat';
    const POST_ID = 2;
    const POST_URL_KEY = 'post';

    const ROUTE_TO_BLOG_CONFIG_VALUE = 'blog';

    const PATH_BLOG = 'blog';
    const PATH_NON_BLOG = 'about-us';

    /**
     * @var \Aheadworks\Blog\Controller\Router
     */
    private $router;

    /**
     * @var \Magento\Framework\App\Action\Forward|\PHPUnit_Framework_MockObject_MockObject
     */
    private $action;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->action = $this->getMock('Magento\Framework\App\Action\Forward', [], [], '', false);
        $actionFactoryStub = $this->getMock('Magento\Framework\App\ActionFactory', ['create'], [], '', false);
        $actionFactoryStub->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Magento\Framework\App\Action\Forward'))
            ->will($this->returnValue($this->action));

        $categoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $categoryStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $categoryRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $categoryRepositoryStub->expects($this->any())
            ->method('getByUrlKey')
            ->will(
                $this->returnCallback(
                    function ($urlKey) use ($categoryStub) {
                        if ($urlKey == self::CATEGORY_URL_KEY) {
                            return $categoryStub;
                        }
                        throw new \Magento\Framework\Exception\NoSuchEntityException();
                    }
                )
            );

        $postStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $postStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $postRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface');
        $postRepositoryStub->expects($this->any())
            ->method('getByUrlKey')
            ->will(
                $this->returnCallback(
                    function ($urlKey) use ($postStub) {
                        if ($urlKey == self::POST_URL_KEY) {
                            return $postStub;
                        }
                        throw new \Magento\Framework\Exception\NoSuchEntityException();
                    }
                )
            );

        $configStub = $this->getMock('Aheadworks\Blog\Model\Config', ['getValue'], [], '', false);
        $configStub->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo(Config::XML_GENERAL_ROUTE_TO_BLOG))
            ->will($this->returnValue(self::ROUTE_TO_BLOG_CONFIG_VALUE));

        $this->request = $this->getMock(
            'Magento\Framework\App\Request\Http',
            [
                'getPathInfo',
                'setModuleName',
                'setControllerName',
                'setActionName',
                'setParams'
            ],
            [],
            '',
            false
        );
        $this->request->expects($this->any())
            ->method('setModuleName')
            ->will($this->returnSelf());
        $this->request->expects($this->any())
            ->method('setControllerName')
            ->will($this->returnSelf());
        $this->request->expects($this->any())
            ->method('setActionName')
            ->will($this->returnSelf());
        $this->request->expects($this->any())
            ->method('setParams')
            ->will($this->returnSelf());

        $this->router = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Router',
            [
                'actionFactory' => $actionFactoryStub,
                'categoryRepository' => $categoryRepositoryStub,
                'postRepository' => $postRepositoryStub,
                'config' => $configStub
            ]
        );
    }

    /**
     * Testing return value of match method if blog path
     */
    public function testMatchResultForBlogPath()
    {
        $this->request->expects($this->any())
            ->method('getPathInfo')
            ->willReturn(self::PATH_BLOG);
        $this->assertSame($this->action, $this->router->match($this->request));
    }

    /**
     * Testing return value of match method if non blog path
     */
    public function testMatchResultForNonBlogPath()
    {
        $this->request->expects($this->any())
            ->method('getPathInfo')
            ->willReturn(self::PATH_NON_BLOG);
        $this->assertFalse($this->router->match($this->request));
    }

    /**
     * Testing that blog pages are matched correctly
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch($path, $controllerName, $actionName, $params)
    {
        $this->request->expects($this->any())
            ->method('getPathInfo')
            ->willReturn($path);
        $this->request->expects($this->atLeastOnce())
            ->method('setModuleName')
            ->with($this->equalTo('aw_blog'));
        $this->request->expects($this->atLeastOnce())
            ->method('setControllerName')
            ->with($this->equalTo($controllerName));
        $this->request->expects($this->atLeastOnce())
            ->method('setActionName')
            ->with($this->equalTo($actionName));
        if ($params) {
            $this->request->expects($this->atLeastOnce())
                ->method('setParams')
                ->with($this->equalTo($params));
        }
        $this->router->match($this->request);
    }

    /**
     * @return array
     */
    public function matchDataProvider()
    {
        return [
            'blog home page' => ['blog', 'index', 'index', null],
            'blog category page' => [
                'blog/cat',
                'category',
                'view',
                ['blog_category_id' => self::CATEGORY_ID]
            ],
            'blog post page' => [
                'blog/post',
                'post',
                'view',
                ['post_id' => self::POST_ID]
            ],
            'blog category post page' => [
                'blog/cat/post',
                'post',
                'view',
                [
                    'blog_category_id' => self::CATEGORY_ID,
                    'post_id' => self::POST_ID
                ]
            ],
            'blog tag search page' => [
                'blog/tag/tag+name',
                'index',
                'index',
                ['tag' => 'tag name']
            ]
        ];
    }
}
