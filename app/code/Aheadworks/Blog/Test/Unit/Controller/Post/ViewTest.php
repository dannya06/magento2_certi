<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Post;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Post\View
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    const POST_ID = 1;
    const POST_VIRTUAL_STATUS = 'published';
    const POST_TITLE = 'Post';
    const POST_META_DESCRIPTION = 'Meta description';
    const CATEGORY_ID = 1;

    const STORE_ID = 1;
    const ERROR_MESSAGE = 'Not found.';
    const REFERER_URL = 'http://localhost';
    const POST_URL = 'http://localhost/post';

    /**
     * @var array
     */
    private $postStoreIds = [self::STORE_ID, 2];

    /**
     * @var array
     */
    private $postCategoryIds = [self::CATEGORY_ID, 2];

    /**
     * @var \Aheadworks\Blog\Controller\Post\View
     */
    private $action;

    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPage;

    /**
     * @var \Magento\Framework\Controller\Result\Forward|\PHPUnit_Framework_MockObject_MockObject
     */
    private $forward;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirect;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pageConfig;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    private $title;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManager;

    /**
     * @var \Aheadworks\Blog\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    private $url;

    /**
     * @var \Aheadworks\Blog\Api\PostRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postRepository;

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

        $this->title = $this->getMock('Magento\Framework\View\Page\Title', ['set'], [], '', false);
        $this->pageConfig = $this->getMock(
            'Magento\Framework\View\Page\Config',
            ['getTitle', 'setMetadata'],
            [],
            '',
            false
        );
        $this->pageConfig->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue($this->title));
        $this->resultPage = $this->getMock('Magento\Framework\View\Result\Page', ['getConfig'], [], '', false);
        $this->resultPage->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($this->pageConfig));
        $resultPageFactoryStub = $this->getMock(
            'Magento\Framework\View\Result\PageFactory',
            ['create'],
            [],
            '',
            false
        );
        $resultPageFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultPage));

        $this->forward = $this->getMock(
            'Magento\Framework\Controller\Result\Forward',
            [
                'setModule',
                'setController',
                'forward'
            ],
            [],
            '',
            false
        );
        $this->forward->expects($this->any())
            ->method('setModule')
            ->will($this->returnSelf());
        $this->forward->expects($this->any())
            ->method('setController')
            ->will($this->returnSelf());
        $this->forward->expects($this->any())
            ->method('forward')
            ->will($this->returnSelf());
        $resultForwardFactoryStub = $this->getMock(
            'Magento\Framework\Controller\Result\ForwardFactory',
            ['create'],
            [],
            '',
            false
        );
        $resultForwardFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->forward));

        $storeStub = $this->getMockForAbstractClass('Magento\Store\Api\Data\StoreInterface');
        $storeStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::STORE_ID));
        $storeManagerStub = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $storeManagerStub->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeStub));

        $this->post = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $this->postRepository = $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface');
        $this->postRepository->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::POST_ID))
            ->will($this->returnValue($this->post));

        $this->url = $this->getMock('Aheadworks\Blog\Model\Url', ['getPostUrl'], [], '', false);
        $this->url->expects($this->any())
            ->method('getPostUrl')
            ->with($this->equalTo($this->post))
            ->will($this->returnValue(self::POST_URL));

        $this->resultRedirect = $this->getMock(
            'Magento\Framework\Controller\Result\Redirect',
            ['setUrl'],
            [],
            '',
            false
        );
        $resultRedirectFactoryStub = $this->getMock(
            'Magento\Framework\Controller\Result\RedirectFactory',
            ['create'],
            [],
            '',
            false
        );
        $resultRedirectFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultRedirect));

        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $redirectStub = $this->getMockForAbstractClass('Magento\Framework\App\Response\RedirectInterface');
        $redirectStub->expects($this->any())
            ->method('getRefererUrl')
            ->will($this->returnValue(self::REFERER_URL));
        $this->messageManager = $this->getMockForAbstractClass('Magento\Framework\Message\ManagerInterface');
        $context = $objectManager->getObject(
            'Magento\Framework\App\Action\Context',
            [
                'request' => $this->request,
                'redirect' => $redirectStub,
                'messageManager' => $this->messageManager,
                'resultRedirectFactory' => $resultRedirectFactoryStub
            ]
        );

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Post\View',
            [
                'context' => $context,
                'resultPageFactory' => $resultPageFactoryStub,
                'resultForwardFactory' => $resultForwardFactoryStub,
                'storeManager' => $storeManagerStub,
                'postRepository' => $this->postRepository,
                'url' => $this->url
            ]
        );
    }

    /**
     * Prepare post mock
     *
     * @param string $virtualStatus
     * @param array|null $storeIds
     * @param array|null $categoryIds
     */
    private function preparePostMock($virtualStatus = self::POST_VIRTUAL_STATUS, $storeIds = null, $categoryIds = null)
    {
        if (!$storeIds) {
            $storeIds = $this->postStoreIds;
        }
        if (!$categoryIds) {
            $categoryIds = $this->postCategoryIds;
        }
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue(self::POST_TITLE));
        $this->post->expects($this->any())
            ->method('getMetaDescription')
            ->will($this->returnValue(self::POST_META_DESCRIPTION));
        $this->post->expects($this->any())
            ->method('getCategoryIds')
            ->will($this->returnValue($categoryIds));
        $this->post->expects($this->any())
            ->method('getVirtualStatus')
            ->will($this->returnValue($virtualStatus));
        $this->post->expects($this->any())
            ->method('getStoreIds')
            ->will($this->returnValue($storeIds));
    }

    /**
     * Testing return value of execute method
     */
    public function testExecuteResult()
    {
        $this->preparePostMock();
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['post_id', null, self::POST_ID],
                    ['blog_category_id', null, null]
                ]
            );
        $this->assertSame($this->resultPage, $this->action->execute());
    }

    /**
     * Testing redirect if error is occur
     */
    public function testExecuteErrorRedirect()
    {
        $this->preparePostMock();
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['post_id', null, self::POST_ID],
                    ['blog_category_id', null, null]
                ]
            );
        $this->postRepository->expects($this->any())
            ->method('get')
            ->willThrowException(
                new \Magento\Framework\Exception\LocalizedException(__(self::ERROR_MESSAGE))
            );
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }

    /**
     * Testing that error message is added if error is occur
     */
    public function testExecuteErrorMessage()
    {
        $this->preparePostMock();
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['post_id', null, self::POST_ID],
                    ['blog_category_id', null, null]
                ]
            );
        $this->postRepository->expects($this->any())
            ->method('get')
            ->willThrowException(
                new \Magento\Framework\Exception\LocalizedException(__(self::ERROR_MESSAGE))
            );
        $this->messageManager->expects($this->once())
            ->method('addError')
            ->with($this->equalTo(self::ERROR_MESSAGE));
        $this->action->execute();
    }

    /**
     * Testing that page config values is set
     */
    public function testExecutePageConfig()
    {
        $this->preparePostMock();
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['post_id', null, self::POST_ID],
                    ['blog_category_id', null, null]
                ]
            );
        $this->title->expects($this->atLeastOnce())
            ->method('set')
            ->with($this->equalTo(self::POST_TITLE));
        $this->pageConfig->expects($this->atLeastOnce())
            ->method('setMetadata')
            ->with(
                $this->equalTo('description'),
                $this->equalTo(self::POST_META_DESCRIPTION)
            );
        $this->action->execute();
    }

    /**
     * Testing of forwarding to noroute action if post is not valid
     *
     * @dataProvider executeForwardDataProvider
     */
    public function testExecuteForward($status, $storeIds)
    {
        $this->preparePostMock($status, $storeIds);
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['post_id', null, self::POST_ID],
                    ['blog_category_id', null, null]
                ]
            );
        $this->forward->expects($this->atLeastOnce())
            ->method('setModule')
            ->with($this->equalTo('cms'));
        $this->forward->expects($this->atLeastOnce())
            ->method('setController')
            ->with($this->equalTo('noroute'));
        $this->forward->expects($this->once())
            ->method('forward')
            ->with($this->equalTo('index'));
        $this->action->execute();
    }

    /**
     * Testing of redirect to post page if category id request param is not belongs to the post
     */
    public function testExecuteRedirectWhenInvalidCategoryId()
    {
        $this->preparePostMock(self::POST_VIRTUAL_STATUS, null, [2]);
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['post_id', null, self::POST_ID],
                    ['blog_category_id', null, self::CATEGORY_ID]
                ]
            );
        $this->resultRedirect->expects($this->atLeastOnce())
            ->method('setUrl')
            ->with($this->equalTo(self::POST_URL));
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }

    /**
     * @return array
     */
    public function executeForwardDataProvider()
    {
        return [
            'post is draft' => ['draft', null],
            'post is scheduled' => ['scheduled', null],
            'post from another store view' => ['published', [2]]
        ];
    }
}
