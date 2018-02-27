<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Test\Unit\Controller\Adminhtml\Event\Email;

use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\StatisticsInterface;
use Aheadworks\Followupemail2\Controller\Adminhtml\Event\Email\Delete;
use Aheadworks\Followupemail2\Api\CampaignManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\EmailRepositoryInterface;
use Aheadworks\Followupemail2\Api\StatisticsManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Test for \Aheadworks\Followupemail2\Test\Unit\Controller\Adminhtml\Event\Email\Delete
 */
class DeleteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Delete
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
     * @var CampaignManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $campaignManagementMock;

    /**
     * @var EventRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventRepositoryMock;

    /**
     * @var EventRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailRepositoryMock;

    /**
     * @var StatisticsManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticsManagementMock;

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
        $this->campaignManagementMock = $this->getMockBuilder(CampaignManagementInterface::class)
            ->getMockForAbstractClass();
        $this->eventRepositoryMock = $this->getMockBuilder(EventRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->emailRepositoryMock = $this->getMockBuilder(EmailRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->statisticsManagementMock = $this->getMockBuilder(StatisticsManagementInterface::class)
            ->getMockForAbstractClass();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->setMethods(['buildOutputDataArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = $objectManager->getObject(
            Delete::class,
            [
                'context' => $this->contextMock,
                'resultJsonFactory' => $this->resultJsonFactoryMock,
                'campaignManagement' => $this->campaignManagementMock,
                'eventRepository' => $this->eventRepositoryMock,
                'emailRepository' => $this->emailRepositoryMock,
                'statisticsManagement' => $this->statisticsManagementMock,
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
        $campaignId = 3;
        $eventId = 2;
        $eventsCount = 4;
        $emailsCount = 5;
        $stats = [
            'sent' => 3,
            'opened' => 2,
            'clicks' => 1,
            'open_rate' => 66.66,
            'click_rate' => 50.00,
        ];
        $result =  [
            'error'     => false,
            'message'   => __('Success.'),
            'events_count' => $eventsCount,
            'emails_count' => $emailsCount,
            'campaign_stats' => $stats
        ];

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with($requestParam)
            ->willReturn($emailId);

        $emailMock = $this->getMockBuilder(EmailInterface::class)
            ->getMockForAbstractClass();
        $emailMock->expects($this->once())
            ->method('getEventId')
            ->willReturn($eventId);
        $this->emailRepositoryMock->expects($this->once())
            ->method('get')
            ->with($emailId)
            ->willReturn($emailMock);

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventMock->expects($this->once())
            ->method('getCampaignId')
            ->willReturn($campaignId);
        $this->eventRepositoryMock->expects($this->once())
            ->method('get')
            ->with($eventId)
            ->willReturn($eventMock);

        $this->emailRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($emailId)
            ->willReturn(true);

        $this->campaignManagementMock->expects($this->once())
            ->method('getEventsCount')
            ->with($campaignId)
            ->willReturn($eventsCount);
        $this->campaignManagementMock->expects($this->once())
            ->method('getEmailsCount')
            ->with($campaignId)
            ->willReturn($emailsCount);

        $statisticsMock = $this->getMockBuilder(StatisticsInterface::class)
            ->getMockForAbstractClass();
        $this->statisticsManagementMock->expects($this->once())
            ->method('getByCampaignId')
            ->with($campaignId)
            ->willReturn($statisticsMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($statisticsMock, StatisticsInterface::class)
            ->willReturn($stats);

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

        $this->emailRepositoryMock->expects($this->once())
            ->method('deleteById')
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
