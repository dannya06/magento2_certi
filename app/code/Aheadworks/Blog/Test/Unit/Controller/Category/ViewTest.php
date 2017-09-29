<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Category\View
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    const CATEGORY_ID = 1;
    const CATEGORY_STATUS = 1;
    const CATEGORY_NAME = 'Category';
    const CATEGORY_META_DESCRIPTION = 'Meta description';

    const STORE_ID = 1;
    const ERROR_MESSAGE = 'Not found.';
    const REFERER_URL = 'http://localhost';

    /**
     * @var array
     */
    private $categoryStoreIds = [self::STORE_ID, 2];

    /**
     * @var \Aheadworks\Blog\Controller\Category\View
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
     * @var \Aheadworks\Blog\Api\CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepository;

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
        $resultPageFactoryStub = $this->getMock('Magento\Framework\View\Result\PageFactory', ['create'], [], '', false);
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

        $this->category = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $this->categoryRepository = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $this->categoryRepository->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::CATEGORY_ID))
            ->will($this->returnValue($this->category));

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

        $requestStub = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $requestStub->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('blog_category_id'))
            ->will($this->returnValue(self::CATEGORY_ID));
        $redirectStub = $this->getMockForAbstractClass('Magento\Framework\App\Response\RedirectInterface');
        $redirectStub->expects($this->any())
            ->method('getRefererUrl')
            ->will($this->returnValue(self::REFERER_URL));
        $this->messageManager = $this->getMockForAbstractClass('Magento\Framework\Message\ManagerInterface');
        $context = $objectManager->getObject(
            'Magento\Framework\App\Action\Context',
            [
                'request' => $requestStub,
                'redirect' => $redirectStub,
                'messageManager' => $this->messageManager,
                'resultRedirectFactory' => $resultRedirectFactoryStub
            ]
        );

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Category\View',
            [
                'context' => $context,
                'resultPageFactory' => $resultPageFactoryStub,
                'resultForwardFactory' => $resultForwardFactoryStub,
                'storeManager' => $storeManagerStub,
                'categoryRepository' => $this->categoryRepository
            ]
        );
    }

    /**
     * Prepare category mock
     *
     * @param int $status
     * @param array|null $storeIds
     */
    private function prepareCategoryMock($status = self::CATEGORY_STATUS, $storeIds = null)
    {
        if (!$storeIds) {
            $storeIds = $this->categoryStoreIds;
        }
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::CATEGORY_NAME));
        $this->category->expects($this->any())
            ->method('getMetaDescription')
            ->will($this->returnValue(self::CATEGORY_META_DESCRIPTION));
        $this->category->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue($status));
        $this->category->expects($this->any())
            ->method('getStoreIds')
            ->will($this->returnValue($storeIds));
    }

    /**
     * Testing return value of execute method
     */
    public function testExecuteResult()
    {
        $this->prepareCategoryMock();
        $this->assertSame($this->resultPage, $this->action->execute());
    }

    /**
     * Testing redirect if error is occur
     */
    public function testExecuteErrorRedirect()
    {
        $this->prepareCategoryMock();
        $this->categoryRepository->expects($this->any())
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
        $this->prepareCategoryMock();
        $this->categoryRepository->expects($this->any())
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
        $this->prepareCategoryMock();
        $this->title->expects($this->atLeastOnce())
            ->method('set')
            ->with($this->equalTo(self::CATEGORY_NAME));
        $this->pageConfig->expects($this->atLeastOnce())
            ->method('setMetadata')
            ->with(
                $this->equalTo('description'),
                $this->equalTo(self::CATEGORY_META_DESCRIPTION)
            );
        $this->action->execute();
    }

    /**
     * Testing of forwarding to noroute action if category is not valid
     *
     * @dataProvider executeForwardDataProvider
     */
    public function testExecuteForward($status, $storeIds)
    {
        $this->prepareCategoryMock($status, $storeIds);
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
     * @return array
     */
    public function executeForwardDataProvider()
    {
        return [
            'category is disabled' => [0, null],
            'category from another store view' => [1, [2]]
        ];
    }
}
