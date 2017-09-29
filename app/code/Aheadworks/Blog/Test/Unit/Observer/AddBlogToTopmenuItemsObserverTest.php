<?php
namespace Aheadworks\Blog\Test\Unit\Observer;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Observer\AddBlogToTopmenuItemsObserver
 */
class AddBlogToTopmenuItemsObserverTest extends \PHPUnit_Framework_TestCase
{
    const BLOG_TITLE_CONFIG_VALUE = 'blog';
    const STORE_ID = 1;

    const CATEGORY_ID = 1;
    const CATEGORY_ID_NOT_MATCH = 2;
    const CATEGORY_NAME = 'category';

    const BLOG_HOME_URL = 'http://localhost/blog';
    const CATEGORY_URL = 'http://localhost/blog/cat';

    /**
     * @var \Aheadworks\Blog\Observer\AddBlogToTopmenuItemsObserver
     */
    private $observer;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategorySearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResult;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    /**
     * @var \Magento\Framework\Data\Tree\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    private $menu;

    /**
     * @var \Magento\Framework\Data\Tree\NodeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $nodeFactory;

    /**
     * @var \Magento\Framework\Data\Tree\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blogHomeNode;

    /**
     * @var \Magento\Framework\Data\Tree\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryNode;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $observerMock;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $searchCriteriaStub = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilder = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaBuilder')
            ->setMethods(['addFilter', 'addSortOrder', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('addFilter')
            ->will($this->returnSelf());
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('addSortOrder')
            ->will($this->returnSelf());
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('create')
            ->will($this->returnValue($searchCriteriaStub));

        $configStub = $this->getMockBuilder('Aheadworks\Blog\Model\Config')
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $configStub->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo(Config::XML_GENERAL_BLOG_TITLE))
            ->will($this->returnValue(self::BLOG_TITLE_CONFIG_VALUE));

        $this->coreRegistry = $this->getMockBuilder('Magento\Framework\Registry')
            ->setMethods(['registry'])
            ->disableOriginalConstructor()
            ->getMock();

        $storeStub = $this->getMockForAbstractClass('Magento\Store\Api\Data\StoreInterface');
        $storeStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::STORE_ID));
        $storeManagerStub = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $storeManagerStub->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeStub));

        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $this->category = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::CATEGORY_NAME));

        $urlStub = $this->getMockBuilder('Aheadworks\Blog\Model\Url')
            ->setMethods(['getCategoryUrl', 'getBlogHomeUrl'])
            ->disableOriginalConstructor()
            ->getMock();
        $urlStub->expects($this->any())
            ->method('getCategoryUrl')
            ->with($this->equalTo($this->category))
            ->will($this->returnValue(self::CATEGORY_URL));
        $urlStub->expects($this->any())
            ->method('getBlogHomeUrl')
            ->will($this->returnValue(self::BLOG_HOME_URL));

        $this->searchResult = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategorySearchResultsInterface');
        $categoryRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $categoryRepositoryStub->expects($this->any())
            ->method('getList')
            ->will($this->returnValue($this->searchResult));

        $tree = $this->getMockBuilder('Magento\Framework\Data\Tree')
            ->disableOriginalConstructor()
            ->getMock();
        $this->nodeFactory = $this->getMockBuilder('Magento\Framework\Data\Tree\NodeFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->blogHomeNode = $this->getMockBuilder('Magento\Framework\Data\Tree\Node')
            ->setMethods(['getTree', 'addChild'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->blogHomeNode->expects($this->any())
            ->method('getTree')
            ->will($this->returnValue($tree));
        $this->categoryNode = $this->getMockBuilder('Magento\Framework\Data\Tree\Node')
            ->setMethods(['getTree', 'addChild'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryNode->expects($this->any())
            ->method('getTree')
            ->will($this->returnValue($tree));

        $menuBlockStub = $this->getMockBuilder('Magento\Theme\Block\Html\Topmenu')
            ->setMethods(['addIdentity'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventStub = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventStub->expects($this->any())
            ->method('getBlock')
            ->will($this->returnValue($menuBlockStub));

        $this->menu = $this->getMockBuilder('Magento\Framework\Data\Tree\Node')
            ->setMethods(['getTree', 'addChild'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->menu->expects($this->any())
            ->method('getTree')
            ->will($this->returnValue($tree));

        $this->observerMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->setMethods(['getEvent', 'getMenu'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->observerMock->expects($this->any())
            ->method('getEvent')
            ->will($this->returnValue($eventStub));
        $this->observerMock->expects($this->any())
            ->method('getMenu')
            ->will($this->returnValue($this->menu));

        $this->observer = $objectManager->getObject(
            'Aheadworks\Blog\Observer\AddBlogToTopmenuItemsObserver',
            [
                'categoryRepository' => $categoryRepositoryStub,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilder,
                'url' => $urlStub,
                'config' => $configStub,
                'coreRegistry' => $this->coreRegistry,
                'storeManager' => $storeManagerStub,
                'request' => $this->request,
                'nodeFactory' => $this->nodeFactory
            ]
        );
    }

    /**
     * Testing that arguments of node factory 'create' method are correct
     */
    public function testNewNodeCreateArguments()
    {
        $this->searchResult->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([]));
        $this->nodeFactory->expects($this->any())
            ->method('create')
            ->with(
                $this->logicalAnd(
                    $this->arrayHasKey('data'),
                    $this->arrayHasKey('idField'),
                    $this->arrayHasKey('tree'),
                    $this->arrayHasKey('parent')
                )
            );
        $this->observer->execute($this->observerMock);
    }

    /**
     * Testing that the blog home item is added to menu
     */
    public function testBlogHomeItemIsAdded()
    {
        $this->searchResult->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([]));
        $this->nodeFactory->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(
                    function ($args) {
                        $data = $args['data'];
                        return isset($data['name']) && $data['name'] == self::BLOG_TITLE_CONFIG_VALUE
                            && isset($data['url']) && $data['url'] == self::BLOG_HOME_URL;
                    }
                )
            )
            ->willReturn($this->blogHomeNode);
        $this->menu->expects($this->once())
            ->method('addChild')
            ->with($this->equalTo($this->blogHomeNode));
        $this->observer->execute($this->observerMock);
    }

    /**
     * Testing that the category item is added to menu
     */
    public function testCategoryItemIsAdded()
    {
        $this->searchResult->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([$this->category]));
        $this->nodeFactory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive(
                [$this->anything()],
                [
                    $this->callback(
                        function ($args) {
                            $data = $args['data'];
                            return isset($data['name']) && $data['name'] == self::CATEGORY_NAME
                            && isset($data['url']) && $data['url'] == self::CATEGORY_URL;
                        }
                    )
                ]
            )
            ->will($this->onConsecutiveCalls($this->blogHomeNode, $this->categoryNode));
        $this->blogHomeNode->expects($this->once())
            ->method('addChild')
            ->with($this->equalTo($this->categoryNode));
        $this->observer->execute($this->observerMock);
    }

    /**
     * Testing that the blog home item is not active in the all pages except blog
     */
    public function testBlogHomeItemIsNotActive()
    {
        $this->searchResult->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([]));
        $this->coreRegistry->expects($this->any())
            ->method('registry')
            ->with($this->equalTo('aw_blog_action'))
            ->willReturn(null);
        $this->nodeFactory->expects($this->any())
            ->method('create')
            ->with(
                $this->callback(
                    function ($args) {
                        $data = $args['data'];
                        return $data['is_active'] == false;
                    }
                )
            );
        $this->observer->execute($this->observerMock);
    }

    /**
     * Testing that the blog home item is active in the blog pages
     */
    public function testBlogHomeItemIsActive()
    {
        $this->searchResult->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([]));
        $this->coreRegistry->expects($this->any())
            ->method('registry')
            ->with($this->equalTo('aw_blog_action'))
            ->willReturn(1);
        $this->nodeFactory->expects($this->any())
            ->method('create')
            ->with(
                $this->callback(
                    function ($args) {
                        $data = $args['data'];
                        return $data['is_active'] == true;
                    }
                )
            );
        $this->observer->execute($this->observerMock);
    }

    /**
     * Testing that the category item is not active in the all pages except blog category with matching ID
     *
     * @dataProvider categoryItemIsNotActiveDataProvider
     */
    public function testCategoryItemIsNotActive($blogAction, $categoryId)
    {
        $this->searchResult->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([$this->category]));
        $this->coreRegistry->expects($this->any())
            ->method('registry')
            ->with($this->equalTo('aw_blog_action'))
            ->willReturn($blogAction);
        $this->request->expects($this->any())
            ->method('getParam')
            ->with('blog_category_id')
            ->willReturn($categoryId);
        $this->nodeFactory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive(
                [$this->anything()],
                [
                    $this->callback(
                        function ($args) {
                            $data = $args['data'];
                            return $data['is_active'] == false;
                        }
                    )
                ]
            )
            ->will($this->onConsecutiveCalls($this->blogHomeNode, $this->categoryNode));
        $this->observer->execute($this->observerMock);
    }

    /**
     * Testing that the category item is active in the blog category page
     */
    public function testCategoryItemIsActive()
    {
        $this->searchResult->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue([$this->category]));
        $this->request->expects($this->any())
            ->method('getParam')
            ->with('blog_category_id')
            ->willReturn(self::CATEGORY_ID);
        $this->nodeFactory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive(
                [$this->anything()],
                [
                    $this->callback(
                        function ($args) {
                            $data = $args['data'];
                            return $data['is_active'] == true;
                        }
                    )
                ]
            )
            ->will($this->onConsecutiveCalls($this->blogHomeNode, $this->categoryNode));
        $this->observer->execute($this->observerMock);
    }

    /**
     * @return array
     */
    public function categoryItemIsNotActiveDataProvider()
    {
        return [
            'non blog actions' => [null, null],
            'blog home action' => [1, null],
            'blog category action, IDs is not matched' => [null, self::CATEGORY_ID_NOT_MATCH]
        ];
    }
}
