<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Adminhtml\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Adminhtml\Category\Edit
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    const CATEGORY_ID = 1;

    /**
     * @var \Aheadworks\Blog\Controller\Adminhtml\Category\Edit
     */
    private $action;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirect;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManager;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPage;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    private $title;

    /**
     * @var \Aheadworks\Blog\Api\CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepository;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->categoryRepository = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');

        $this->title = $this->getMock('Magento\Framework\View\Page\Title', ['prepend'], [], '', false);
        $pageConfigStub = $this->getMock('Magento\Framework\View\Page\Config', ['getTitle'], [], '', false);
        $pageConfigStub->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue($this->title));
        $this->resultPage = $this->getMock(
            'Magento\Backend\Model\View\Result\Page',
            ['setActiveMenu', 'getConfig'],
            [],
            '',
            false
        );
        $this->resultPage->expects($this->any())
            ->method('setActiveMenu')
            ->will($this->returnSelf());
        $this->resultPage->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($pageConfigStub));
        $resultPageFactoryStub = $this->getMock('Magento\Framework\View\Result\PageFactory', ['create'], [], '', false);
        $resultPageFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultPage));

        $this->resultRedirect = $this->getMock(
            'Magento\Framework\Controller\Result\Redirect',
            ['setPath'],
            [],
            '',
            false
        );
        $this->resultRedirect->expects($this->any())
            ->method('setPath')
            ->will($this->returnSelf());
        $resultRedirectFactoryStub = $this->getMock(
            'Magento\Backend\Model\View\Result\RedirectFactory',
            ['create'],
            [],
            '',
            false
        );
        $resultRedirectFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultRedirect));

        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $this->messageManager = $this->getMockForAbstractClass('Magento\Framework\Message\ManagerInterface');
        $context = $objectManager->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $this->request,
                'messageManager' => $this->messageManager,
                'resultRedirectFactory' => $resultRedirectFactoryStub
            ]
        );

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Adminhtml\Category\Edit',
            [
                'context' => $context,
                'categoryRepository' => $this->categoryRepository,
                'resultPageFactory' => $resultPageFactoryStub
            ]
        );
    }

    /**
     * Testing of result of execution if category exists
     */
    public function testExecuteResultCategoryExists()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn(self::CATEGORY_ID);
        $this->assertSame($this->resultPage, $this->action->execute());
    }

    /**
     * Testing of redirection if category is not exists
     */
    public function testExecuteRedirectCategoryNotExists()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn(self::CATEGORY_ID);
        $this->categoryRepository->expects($this->any())
            ->method('get')
            ->willThrowException(
                new \Magento\Framework\Exception\NoSuchEntityException()
            );
        $this->resultRedirect->expects($this->atLeastOnce())
            ->method('setPath')
            ->with($this->equalTo('*/*/index'));
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }

    /**
     * Testing that error message is added if category is not exists
     */
    public function testExecuteErrorMessage()
    {
        $exception = new \Magento\Framework\Exception\NoSuchEntityException();
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn(self::CATEGORY_ID);
        $this->categoryRepository->expects($this->any())
            ->method('get')
            ->willThrowException($exception);
        $this->messageManager->expects($this->once())
            ->method('addException')
            ->with($this->equalTo($exception));
        $this->action->execute();
    }
}
