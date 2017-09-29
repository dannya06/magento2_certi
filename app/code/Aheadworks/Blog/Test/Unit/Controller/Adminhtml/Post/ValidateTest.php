<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Adminhtml\Post;

use Magento\Framework\Message\MessageInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Adminhtml\Post\Validate
 */
class ValidateTest extends \PHPUnit_Framework_TestCase
{
    const POST_ID = 1;
    const USER_ID = 1;
    const USER_NAME = 'Admin Admin';
    const STORE_ID = 1;
    const ERROR_MESSAGE = 'Value is invalid.';

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
     * @var \Aheadworks\Blog\Controller\Adminhtml\Post\Validate
     */
    private $action;

    /**
     * @var \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultJson;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Validator\Exception|\PHPUnit_Framework_MockObject_MockObject
     */
    private $exception;

    /**
     * @var \Aheadworks\Blog\Model\Post|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postModel;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $postStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $postStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $postDataFactoryStub = $this->getMock(
            'Aheadworks\Blog\Api\Data\PostInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $postDataFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($postStub));

        $this->postModel = $this->getMock(
            'Aheadworks\Blog\Model\Post',
            ['setData', 'setPostId', 'validateBeforeSave'],
            [],
            '',
            false
        );
        $postFactoryStub = $this->getMock('Aheadworks\Blog\Model\PostFactory', ['create'], [], '', false);
        $postFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->postModel));

        $dataObjectHelperStub = $this->getMock(
            'Magento\Framework\Api\DataObjectHelper',
            ['populateWithArray'],
            [],
            '',
            false
        );
        $dataObjectProcessorStub = $this->getMock(
            'Magento\Framework\Reflection\DataObjectProcessor',
            ['buildOutputDataArray'],
            [],
            '',
            false
        );

        $this->resultJson = $this->getMock('Magento\Framework\Controller\Result\Json', ['setData'], [], '', false);
        $this->resultJson->expects($this->any())
            ->method('setData')
            ->will($this->returnSelf());
        $resultJsonFactoryStub = $this->getMock(
            'Magento\Framework\Controller\Result\JsonFactory',
            ['create'],
            [],
            '',
            false
        );
        $resultJsonFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultJson));

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

        $errorStub = $this->getMock('Magento\Framework\Message\Error', ['getText'], [], '', false);
        $errorStub->expects($this->any())
            ->method('getText')
            ->will($this->returnValue(self::ERROR_MESSAGE));
        $this->exception = $this->getMock('Magento\Framework\Validator\Exception', ['getMessages'], [], '', false);
        $this->exception->expects($this->any())
            ->method('getMessages')
            ->with(MessageInterface::TYPE_ERROR)
            ->will($this->returnValue([$errorStub]));

        $requestStub = $this->getMock('Magento\Framework\App\Request\Http', ['getPostValue'], [], '', false);
        $requestStub->expects($this->any())
            ->method('getPostValue')
            ->will($this->returnValue($this->formData));

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
                'auth' => $authStub
            ]
        );

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Adminhtml\Post\Validate',
            [
                'context' => $context,
                'postDataFactory' => $postDataFactoryStub,
                'postFactory' => $postFactoryStub,
                'dataObjectHelper' => $dataObjectHelperStub,
                'dataObjectProcessor' => $dataObjectProcessorStub,
                'resultJsonFactory' => $resultJsonFactoryStub,
                'storeManager' => $this->storeManager
            ]
        );
    }

    /**
     * Testing of return value of execute method
     */
    public function testExecuteResult()
    {
        $this->assertSame($this->resultJson, $this->action->execute());
    }

    /**
     * Testing of successful validation result
     */
    public function testExecuteSuccess()
    {
        $this->resultJson->expects($this->atLeastOnce())
            ->method('setData')
            ->with(
                $this->callback(
                    function ($response) {
                        return !$response->getError();
                    }
                )
            );
        $this->action->execute();
    }

    /**
     * Testing of unsuccessful validation result
     */
    public function testExecuteFail()
    {
        $this->postModel->expects($this->any())
            ->method('validateBeforeSave')
            ->willThrowException($this->exception);
        $this->resultJson->expects($this->atLeastOnce())
            ->method('setData')
            ->with(
                $this->callback(
                    function ($response) {
                        return $response->getError() && $response->getMessages() == [self::ERROR_MESSAGE];
                    }
                )
            );
        $this->action->execute();
    }
}
