<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Adminhtml\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Adminhtml\Category\Save
 */
class SaveTest extends \PHPUnit_Framework_TestCase
{
    const CATEGORY_ID = 1;

    /**
     * @var array
     */
    private $formData = ['id' => self::CATEGORY_ID, 'name' => 'Category'];

    /**
     * @var \Aheadworks\Blog\Controller\Adminhtml\Category\Save
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
     * @var \Aheadworks\Blog\Api\CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepository;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->category = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));

        $this->categoryRepository = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $this->categoryRepository->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::CATEGORY_ID))
            ->will($this->returnValue($this->category));
        $this->categoryRepository->expects($this->any())
            ->method('save')
            ->with($this->equalTo($this->category))
            ->will($this->returnValue($this->category));
        $categoryDataFactoryStub = $this->getMock(
            'Aheadworks\Blog\Api\Data\CategoryInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $categoryDataFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->category));

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

        $dataObjectHelperStub = $this->getMock(
            'Magento\Framework\Api\DataObjectHelper',
            ['populateWithArray'],
            [],
            '',
            false
        );

        $requestStub = $this->getMock('Magento\Framework\App\Request\Http', ['getPostValue'], [], '', false);
        $requestStub->expects($this->any())
            ->method('getPostValue')
            ->will($this->returnValue($this->formData));
        $this->messageManager = $this->getMockForAbstractClass('Magento\Framework\Message\ManagerInterface');
        $sessionStub = $this->getMock('Magento\Backend\Model\Session', ['unsFormData', 'setFormData'], [], '', false);
        $context = $objectManager->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $requestStub,
                'messageManager' => $this->messageManager,
                'resultRedirectFactory' => $resultRedirectFactoryStub,
                'session' => $sessionStub
            ]
        );

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Adminhtml\Category\Save',
            [
                'context' => $context,
                'categoryRepository' => $this->categoryRepository,
                'categoryDataFactory' => $categoryDataFactoryStub,
                'dataObjectHelper' => $dataObjectHelperStub
            ]
        );
    }

    /**
     * Testing of redirect while saving
     */
    public function testExecuteRedirect()
    {
        $this->resultRedirect->expects($this->atLeastOnce())
            ->method('setPath')
            ->with($this->equalTo('*/*/'));
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }

    /**
     * Testing of redirect if error is occur
     */
    public function testExecuteRedirectError()
    {
        $this->categoryRepository->expects($this->any())
            ->method('save')
            ->willThrowException(
                new \Magento\Framework\Validator\Exception()
            );
        $this->resultRedirect->expects($this->atLeastOnce())
            ->method('setPath')
            ->with($this->equalTo('*/*/edit'));
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }

    /**
     * Testing that category saved
     */
    public function testExecuteCategorySave()
    {
        $this->categoryRepository->expects($this->atLeastOnce())
            ->method('save')
            ->with($this->equalTo($this->category));
        $this->action->execute();
    }

    /**
     * Testing that success message is added if category is saved
     */
    public function testExecuteSuccessMessage()
    {
        $this->messageManager->expects($this->once())->method('addSuccess');
        $this->action->execute();
    }
}
