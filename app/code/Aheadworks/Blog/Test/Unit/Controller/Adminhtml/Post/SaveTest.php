<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Adminhtml\Post;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Adminhtml\Post\Save
 */
class SaveTest extends \PHPUnit_Framework_TestCase
{
    const POST_ID = 1;
    const USER_ID = 1;
    const USER_NAME = 'Admin Admin';
    const STORE_ID = 1;

    /**
     * @var array
     */
    private $formData = [
        'post' => [
            'id' => self::POST_ID,
            'title' => 'Post',
            'has_short_content' => 'true'
        ]
    ];

    /**
     * @var \Aheadworks\Blog\Controller\Adminhtml\Post\Save
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
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

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

        $this->post = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));

        $this->postRepository = $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface');
        $this->postRepository->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::POST_ID))
            ->will($this->returnValue($this->post));
        $this->postRepository->expects($this->any())
            ->method('save')
            ->with($this->equalTo($this->post))
            ->will($this->returnValue($this->post));
        $postDataFactoryStub = $this->getMock(
            'Aheadworks\Blog\Api\Data\PostInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $postDataFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->post));

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
        $storeStub = $this->getMockForAbstractClass('Magento\Store\Api\Data\StoreInterface');
        $storeStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::STORE_ID));
        $this->storeManager = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeStub));
        $this->storeManager->expects($this->any())
            ->method('hasSingleStore')
            ->will($this->returnValue(false));

        $requestStub = $this->getMock('Magento\Framework\App\Request\Http', ['getPostValue'], [], '', false);
        $requestStub->expects($this->any())
            ->method('getPostValue')
            ->will($this->returnValue($this->formData));
        $this->messageManager = $this->getMockForAbstractClass('Magento\Framework\Message\ManagerInterface');
        $sessionStub = $this->getMock('Magento\Backend\Model\Session', ['unsFormData', 'setFormData'], [], '', false);

        $userStub = $this->getMock('Magento\User\Model\User', ['getId', 'getName'], [], '', false);
        $userStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::USER_ID));
        $userStub->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::USER_NAME));
        $authStub = $this->getMock('Magento\Backend\Model\Auth', ['getUser'], [], '', false);
        $authStub->expects($this->any())->method('getUser')->will($this->returnValue($userStub));

        $context = $objectManager->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $requestStub,
                'messageManager' => $this->messageManager,
                'resultRedirectFactory' => $resultRedirectFactoryStub,
                'session' => $sessionStub,
                'auth' => $authStub
            ]
        );

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Adminhtml\Post\Save',
            [
                'context' => $context,
                'postRepository' => $this->postRepository,
                'postDataFactory' => $postDataFactoryStub,
                'dataObjectHelper' => $dataObjectHelperStub,
                'storeManager' => $this->storeManager
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
        $this->postRepository->expects($this->any())
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
     * Testing that post saved
     */
    public function testExecutePostSave()
    {
        $this->postRepository->expects($this->atLeastOnce())
            ->method('save')
            ->with($this->equalTo($this->post));
        $this->action->execute();
    }

    /**
     * Testing that success message is added if post is saved
     */
    public function testExecuteSuccessMessage()
    {
        $this->messageManager->expects($this->once())->method('addSuccess');
        $this->action->execute();
    }
}
