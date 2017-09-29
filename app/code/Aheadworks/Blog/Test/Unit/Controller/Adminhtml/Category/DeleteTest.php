<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Adminhtml\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Adminhtml\Category\Delete
 */
class DeleteTest extends \PHPUnit_Framework_TestCase
{
    const CATEGORY_ID = 1;
    const ERROR_MESSAGE = 'Cannot delete.';

    /**
     * @var \Aheadworks\Blog\Controller\Adminhtml\Category\Delete
     */
    private $action;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirect;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManager;

    /**
     * @var \Aheadworks\Blog\Api\CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepository;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->categoryRepository = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');

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
            'Aheadworks\Blog\Controller\Adminhtml\Category\Delete',
            [
                'context' => $context,
                'categoryRepository' => $this->categoryRepository
            ]
        );
    }

    /**
     * Testing of redirect if category id request param is not presented
     */
    public function testExecuteRedirect()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn(null);
        $this->resultRedirect->expects($this->atLeastOnce())
            ->method('setPath')
            ->with($this->equalTo('*/*/index'));
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }

    /**
     * Testing of redirect if category id request param is presented
     */
    public function testExecuteRedirectCategoryIdParam()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn(self::CATEGORY_ID);
        $this->resultRedirect->expects($this->atLeastOnce())
            ->method('setPath')
            ->with($this->equalTo('*/*/index'));
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }

    /**
     * Testing of redirect if error is occur
     */
    public function testExecuteRedirectException()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn(self::CATEGORY_ID);
        $this->categoryRepository->expects($this->any())
            ->method('deleteById')
            ->willThrowException(
                new \Magento\Framework\Exception\LocalizedException(__(self::ERROR_MESSAGE))
            );
        $this->resultRedirect->expects($this->atLeastOnce())
            ->method('setPath')
            ->with($this->equalTo('*/*/index'));
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }

    /**
     * Testing that category is deleted
     */
    public function testExecuteCategoryDelete()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn(self::CATEGORY_ID);
        $this->categoryRepository->expects($this->once())
            ->method('deleteById')
            ->with($this->equalTo(self::CATEGORY_ID));
        $this->action->execute();
    }

    /**
     * Testing that success message is added if category is deleted
     */
    public function testExecuteSuccessMessage()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn(self::CATEGORY_ID);
        $this->messageManager->expects($this->once())->method('addSuccess');
        $this->action->execute();
    }

    /**
     * Testing that error message is added if error is occur
     */
    public function testExecuteErrorMessage()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn(self::CATEGORY_ID);
        $this->categoryRepository->expects($this->any())
            ->method('deleteById')
            ->willThrowException(
                new \Magento\Framework\Exception\LocalizedException(__(self::ERROR_MESSAGE))
            );
        $this->messageManager->expects($this->atLeastOnce())->method('addError');
        $this->action->execute();
    }
}
