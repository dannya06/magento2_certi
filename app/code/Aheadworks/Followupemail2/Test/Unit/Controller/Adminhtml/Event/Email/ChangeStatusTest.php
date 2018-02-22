<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Test\Unit\Controller\Adminhtml\Event\Email;

use Aheadworks\Followupemail2\Controller\Adminhtml\Event\Email\ChangeStatus;
use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\EmailManagementInterface;
use Aheadworks\Followupemail2\Model\Source\Email\Status as EmailStatusSource;
use Aheadworks\Followupemail2\Ui\DataProvider\Event\ManageFormProcessor;
use Aheadworks\Followupemail2\Api\Data\StatisticsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Test for \Aheadworks\Followupemail2\Test\Unit\Controller\Adminhtml\Event\Email\ChangeStatus
 */
class ChangeStatusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ChangeStatus
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var JsonFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultJsonFactoryMock;

    /**
     * @var EmailManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailManagementMock;

    /**
     * @var EmailStatusSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailStatusSourceMock;

    /**
     * @var ManageFormProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manageFormProcessorMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->getMockForAbstractClass();

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
            ]
        );

        $this->resultJsonFactoryMock = $this->getMockBuilder(JsonFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->emailManagementMock = $this->getMockBuilder(EmailManagementInterface::class)
            ->getMockForAbstractClass();
        $this->emailStatusSourceMock = $this->getMockBuilder(EmailStatusSource::class)
            ->setMethods(['getOptionByValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->manageFormProcessorMock = $this->getMockBuilder(ManageFormProcessor::class)
            ->setMethods(['getWhen'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->setMethods(['buildOutputDataArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = $objectManager->getObject(
            ChangeStatus::class,
            [
                'context' => $this->contextMock,
                'resultJsonFactory' => $this->resultJsonFactoryMock,
                'emailManagement' => $this->emailManagementMock,
                'emailStatusSource' => $this->emailStatusSourceMock,
                'manageFormProcessor' => $this->manageFormProcessorMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $requestParam = 'id';
        $emailId = 10;
        $emailStatus = 1;
        $whenLabel = __('Immediately after event triggered');
        $statusLabel = __('Enabled');
        $emailData = [
            'sent' => 3,
            'opened' => 2,
            'clicks' => 1,
            'open_rate' => 66.66,
            'click_rate' => 50.00,
        ];
        $result =  [
            'error'     => false,
            'message'   => __('Success.'),
            'email'     => [
                'when' => $whenLabel,
                'sent' => 3,
                'opened' => 2,
                'clicks' => 1,
                'open_rate' => 66.66,
                'click_rate' => 50.00,
                'status' => $statusLabel,
                'is_email_disabled' => false
            ]
        ];

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with($requestParam)
            ->willReturn($emailId);

        $emailMock = $this->getMockBuilder(EmailInterface::class)
            ->getMockForAbstractClass();
        $emailMock->expects($this->atLeastOnce())
            ->method('getStatus')
            ->willReturn($emailStatus);
        $this->emailManagementMock->expects($this->once())
            ->method('changeStatus')
            ->with($emailId)
            ->willReturn($emailMock);

        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($emailMock, EmailInterface::class)
            ->willReturn($emailData);

        $statisticsMock = $this->getMockBuilder(StatisticsInterface::class)
            ->getMockForAbstractClass();
        $statisticsMock->expects($this->once())
            ->method('getSent')
            ->willReturn($emailData['sent']);
        $statisticsMock->expects($this->once())
            ->method('getOpened')
            ->willReturn($emailData['opened']);
        $statisticsMock->expects($this->once())
            ->method('getClicked')
            ->willReturn($emailData['clicks']);
        $statisticsMock->expects($this->once())
            ->method('getOpenRate')
            ->willReturn($emailData['open_rate']);
        $statisticsMock->expects($this->once())
            ->method('getClickRate')
            ->willReturn($emailData['click_rate']);
        $this->emailManagementMock->expects($this->once())
            ->method('getStatistics')
            ->willReturn($statisticsMock);

        $this->manageFormProcessorMock->expects($this->once())
            ->method('getWhen')
            ->with($emailMock)
            ->willReturn($whenLabel);

        $this->emailStatusSourceMock->expects($this->once())
            ->method('getOptionByValue')
            ->with($emailStatus)
            ->willReturn($statusLabel);

        $resultJsonMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->setMethods(['setData'])
            ->getMock();
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultJsonMock);

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method when no email id specified
     */
    public function testExecuteNoIdSpecified()
    {
        $requestParam = 'id';
        $result =  [
            'error'     => true,
            'message'   => __('Email Id is not specified!')
        ];

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with($requestParam)
            ->willReturn(null);

        $resultJsonMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->setMethods(['setData'])
            ->getMock();
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultJsonMock);

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method when no email with specified id
     */
    public function testExecuteWithExcepton()
    {
        $requestParam = 'id';
        $requestParamValue = 10;
        $result =  [
            'error'     => true,
            'message'   => __('No such entity.')
        ];
        $exception = new NoSuchEntityException();

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with($requestParam)
            ->willReturn($requestParamValue);

        $this->emailManagementMock->expects($this->once())
            ->method('changeStatus')
            ->with($requestParamValue)
            ->willThrowException($exception);

        $resultJsonMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->setMethods(['setData'])
            ->getMock();
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();

        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultJsonMock);

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }
}
