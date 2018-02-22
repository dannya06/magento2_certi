<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Test\Unit\Controller\Adminhtml\Event\Email;

use Aheadworks\Followupemail2\Controller\Adminhtml\Event\Email\Save;
use Aheadworks\Followupemail2\Api\CampaignManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\EmailRepositoryInterface;
use Aheadworks\Followupemail2\Api\EmailManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\EmailInterfaceFactory;
use Aheadworks\Followupemail2\Api\QueueManagementInterface;
use Aheadworks\Followupemail2\Model\Source\Email\Status as EmailStatusSource;
use Aheadworks\Followupemail2\Ui\DataProvider\Event\ManageFormProcessor;
use Aheadworks\Followupemail2\Api\Data\StatisticsInterface;
use Aheadworks\Followupemail2\Api\StatisticsManagementInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Test for \Aheadworks\Followupemail2\Test\Unit\Controller\Adminhtml\Event\Email\Save
 */
class SaveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Save
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

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
     * @var EmailManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailManagementMock;

    /**
     * @var EmailInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailFactoryMock;

    /**
     * @var QueueManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueManagementMock;

    /**
     * @var EmailStatusSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailStatusSourceMock;

    /**
     * @var ManageFormProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manageFormProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var StatisticsManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticsManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->setMethods(['getPostValue'])
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
        $this->emailManagementMock = $this->getMockBuilder(EmailManagementInterface::class)
            ->getMockForAbstractClass();
        $this->emailFactoryMock = $this->getMockBuilder(EmailInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->queueManagementMock = $this->getMockBuilder(QueueManagementInterface::class)
            ->getMockForAbstractClass();
        $this->emailStatusSourceMock = $this->getMockBuilder(EmailStatusSource::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->manageFormProcessorMock = $this->getMockBuilder(ManageFormProcessor::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->setMethods(['buildOutputDataArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->statisticsManagementMock = $this->getMockBuilder(StatisticsManagementInterface::class)
            ->getMockForAbstractClass();

        $this->controller = $objectManager->getObject(
            Save::class,
            [
                'context' => $this->contextMock,
                'resultJsonFactory' => $this->resultJsonFactoryMock,
                'campaignManagement' => $this->campaignManagementMock,
                'eventRepository' => $this->eventRepositoryMock,
                'emailRepository' => $this->emailRepositoryMock,
                'emailManagement' => $this->emailManagementMock,
                'emailFactory' => $this->emailFactoryMock,
                'queueManagement' => $this->queueManagementMock,
                'emailStatusSource' => $this->emailStatusSourceMock,
                'manageFormProcessor' => $this->manageFormProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'statisticsManagement' => $this->statisticsManagementMock,
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $campaignId = 1;
        $eventId = 2;
        $emailId = 10;
        $emailStatus = EmailInterface::STATUS_ENABLED;
        $emailStatusLabel = 'Enabled';
        $emailPosition = 1;
        $emailContentId1 = 21;
        $eventsCount = 1;
        $emailsCount = 1;
        $postData = [
            'id' => $emailId,
            'event_id' => $eventId,
            'name' => 'Test email name',
            'email_send_days' => 0,
            'email_send_hours' => 1,
            'email_send_minutes' => 0,
            'content' => [
                [
                    'id'=> $emailContentId1,
                    'email_id' => $emailId,
                    'subject' => 'Test subject 1',
                    'content' => 'Test content 1',
                    'use_config' => [
                        'sender_name' => 1,
                        'sender_email' => 1,
                        'header_template' => 1,
                        'footer_template' => 1
                    ]
                ]
            ],
            'position' => 1,
            'ab_testing_mode' => 0,
            'primary_email_content' => EmailInterface::CONTENT_VERSION_A
        ];

        $stats = [
            'sent' => 3,
            'opened' => 2,
            'clicks' => 1,
            'open_rate' => 66.66,
            'click_rate' => 50.00,
        ];

        $emailData = [
            'when' => __('1 hour after event triggered'),
            'sent' => $stats['sent'],
            'opened' => $stats['opened'],
            'clicks' => $stats['clicks'],
            'open_rate' => $stats['open_rate'],
            'click_rate' => $stats['click_rate'],
            'status' => $emailStatusLabel,
            'is_email_disabled' => false,
        ];

        $result =  [
            'error'     => false,
            'message'   => __('Success.'),
            'email' => $emailData,
            'totals' => [],
            'create' => false,
            'continue_edit' => false,
            'events_count' => $eventsCount,
            'emails_count' => $emailsCount,
            'campaign_stats' => $stats
        ];

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($postData);

        $emailMock = $this->getMockBuilder(EmailInterface::class)
            ->getMockForAbstractClass();
        $emailMock->expects($this->once())
            ->method('getId')
            ->willReturn($emailId);
        $emailMock->expects($this->exactly(2))
            ->method('getEventId')
            ->willReturn($eventId);
        $emailMock->expects($this->once())
            ->method('getPosition')
            ->willReturn($emailPosition);
        $emailMock->expects($this->exactly(2))
            ->method('getStatus')
            ->willReturn($emailStatus);
        $this->emailRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->with($emailId)
            ->willReturn($emailMock);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($emailMock, $this->anything(), EmailInterface::class)
            ->willReturnSelf();

        $this->emailRepositoryMock->expects($this->once())
            ->method('save')
            ->with($emailMock)
            ->willReturn($emailMock);

        $statisticsMock = $this->getMockBuilder(StatisticsInterface::class)
            ->getMockForAbstractClass();
        $statisticsMock->expects($this->once())
            ->method('getSent')
            ->willReturn($stats['sent']);
        $statisticsMock->expects($this->once())
            ->method('getOpened')
            ->willReturn($stats['opened']);
        $statisticsMock->expects($this->once())
            ->method('getClicked')
            ->willReturn($stats['clicks']);
        $statisticsMock->expects($this->once())
            ->method('getOpenRate')
            ->willReturn($stats['open_rate']);
        $statisticsMock->expects($this->once())
            ->method('getClickRate')
            ->willReturn($stats['click_rate']);
        $this->emailManagementMock->expects($this->once())
            ->method('getStatistics')
            ->with($emailMock)
            ->willReturn($statisticsMock);

        $this->manageFormProcessorMock->expects($this->once())
            ->method('getWhen')
            ->with($emailMock)
            ->willReturn($emailData['when']);
        $this->manageFormProcessorMock->expects($this->once())
            ->method('getEventTotals')
            ->with($eventId)
            ->willReturn([]);

        $this->dataObjectProcessorMock->expects($this->exactly(2))
            ->method('buildOutputDataArray')
            ->withConsecutive(
                [$emailMock, EmailInterface::class],
                [$statisticsMock, StatisticsInterface::class]
            )
            ->willReturnOnConsecutiveCalls($emailData, $stats);

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventMock->expects($this->once())
            ->method('getCampaignId')
            ->willReturn($campaignId);
        $this->eventRepositoryMock->expects($this->once())
            ->method('get')
            ->with($eventId)
            ->willReturn($eventMock);

        $this->statisticsManagementMock->expects($this->once())
            ->method('getByCampaignId')
            ->with($campaignId)
            ->willReturn($statisticsMock);

        $this->campaignManagementMock->expects($this->once())
            ->method('getEventsCount')
            ->with($campaignId)
            ->willReturn($eventsCount);
        $this->campaignManagementMock->expects($this->once())
            ->method('getEmailsCount')
            ->with($campaignId)
            ->willReturn($emailsCount);

        $this->emailStatusSourceMock->expects($this->once())
            ->method('getOptionByValue')
            ->with($emailStatus)
            ->willReturn($emailStatusLabel);

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

        $this->assertEquals($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method if send test email selected
     */
    public function testExecuteSendTestEmail()
    {
        $campaignId = 1;
        $eventId = 2;
        $emailId = 10;
        $emailStatus = EmailInterface::STATUS_ENABLED;
        $emailStatusLabel = 'Enabled';
        $emailPosition = 1;
        $emailContentId1 = 21;
        $eventsCount = 1;
        $emailsCount = 1;
        $postData = [
            'id' => $emailId,
            'event_id' => $eventId,
            'name' => 'Test email name',
            'email_send_days' => 0,
            'email_send_hours' => 1,
            'email_send_minutes' => 0,
            'content' => [
                [
                    'id'=> $emailContentId1,
                    'email_id' => $emailId,
                    'subject' => 'Test subject 1',
                    'content' => 'Test content 1',
                    'use_config' => [
                        'sender_name' => 1,
                        'sender_email' => 1,
                        'header_template' => 1,
                        'footer_template' => 1
                    ]
                ]
            ],
            'position' => 1,
            'ab_testing_mode' => 0,
            'primary_email_content' => EmailInterface::CONTENT_VERSION_A,
            'sendtest' => 1,
            'content_id' => EmailInterface::CONTENT_VERSION_A
        ];

        $stats = [
            'sent' => 3,
            'opened' => 2,
            'clicks' => 1,
            'open_rate' => 66.66,
            'click_rate' => 50.00,
        ];

        $emailData = [
            'when' => __('1 hour after event triggered'),
            'sent' => $stats['sent'],
            'opened' => $stats['opened'],
            'clicks' => $stats['clicks'],
            'open_rate' => $stats['open_rate'],
            'click_rate' => $stats['click_rate'],
            'status' => $emailStatusLabel,
            'is_email_disabled' => false,
        ];

        $result =  [
            'error'     => false,
            'message'   => __('Email was successfully sent.'),
            'email' => $emailData,
            'totals' => [],
            'create' => false,
            'continue_edit' => false,
            'events_count' => $eventsCount,
            'emails_count' => $emailsCount,
            'campaign_stats' => $stats,
            'send_test' => true
        ];

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($postData);

        $emailMock = $this->getMockBuilder(EmailInterface::class)
            ->getMockForAbstractClass();
        $emailMock->expects($this->once())
            ->method('getId')
            ->willReturn($emailId);
        $emailMock->expects($this->exactly(2))
            ->method('getEventId')
            ->willReturn($eventId);
        $emailMock->expects($this->once())
            ->method('getPosition')
            ->willReturn($emailPosition);
        $emailMock->expects($this->exactly(2))
            ->method('getStatus')
            ->willReturn($emailStatus);
        $this->emailRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->with($emailId)
            ->willReturn($emailMock);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($emailMock, $this->anything(), EmailInterface::class)
            ->willReturnSelf();

        $this->emailRepositoryMock->expects($this->once())
            ->method('save')
            ->with($emailMock)
            ->willReturn($emailMock);

        $statisticsMock = $this->getMockBuilder(StatisticsInterface::class)
            ->getMockForAbstractClass();
        $statisticsMock->expects($this->once())
            ->method('getSent')
            ->willReturn($stats['sent']);
        $statisticsMock->expects($this->once())
            ->method('getOpened')
            ->willReturn($stats['opened']);
        $statisticsMock->expects($this->once())
            ->method('getClicked')
            ->willReturn($stats['clicks']);
        $statisticsMock->expects($this->once())
            ->method('getOpenRate')
            ->willReturn($stats['open_rate']);
        $statisticsMock->expects($this->once())
            ->method('getClickRate')
            ->willReturn($stats['click_rate']);
        $this->emailManagementMock->expects($this->once())
            ->method('getStatistics')
            ->with($emailMock)
            ->willReturn($statisticsMock);

        $this->manageFormProcessorMock->expects($this->once())
            ->method('getWhen')
            ->with($emailMock)
            ->willReturn($emailData['when']);
        $this->manageFormProcessorMock->expects($this->once())
            ->method('getEventTotals')
            ->with($eventId)
            ->willReturn([]);

        $this->dataObjectProcessorMock->expects($this->exactly(2))
            ->method('buildOutputDataArray')
            ->withConsecutive(
                [$emailMock, EmailInterface::class],
                [$statisticsMock, StatisticsInterface::class]
            )
            ->willReturnOnConsecutiveCalls($emailData, $stats);

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventMock->expects($this->once())
            ->method('getCampaignId')
            ->willReturn($campaignId);
        $this->eventRepositoryMock->expects($this->once())
            ->method('get')
            ->with($eventId)
            ->willReturn($eventMock);

        $this->statisticsManagementMock->expects($this->once())
            ->method('getByCampaignId')
            ->with($campaignId)
            ->willReturn($statisticsMock);

        $this->campaignManagementMock->expects($this->once())
            ->method('getEventsCount')
            ->with($campaignId)
            ->willReturn($eventsCount);
        $this->campaignManagementMock->expects($this->once())
            ->method('getEmailsCount')
            ->with($campaignId)
            ->willReturn($emailsCount);

        $this->emailStatusSourceMock->expects($this->once())
            ->method('getOptionByValue')
            ->with($emailStatus)
            ->willReturn($emailStatusLabel);

        $this->queueManagementMock->expects($this->once())
            ->method('sendTest')
            ->with($emailMock, EmailInterface::CONTENT_VERSION_A)
            ->willReturn(true);

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

        $this->assertEquals($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method when no data specified
     */
    public function testExecuteNoDataSpecified()
    {
        $result =  [
            'error'     => true,
            'message'   => __('No data specified!')
        ];

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
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

        $this->assertEquals($resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method when no email with specified id
     */
    public function testExecuteWithExcepton()
    {
        $emailId = 1;
        $postData = [
            'id' => $emailId
        ];

        $result =  [
            'error'     => true,
            'message'   => __('No such entity.')
        ];
        $exception = new NoSuchEntityException();

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($postData);

        $this->emailRepositoryMock->expects($this->once())
            ->method('get')
            ->with($emailId)
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

        $this->assertEquals($resultJsonMock, $this->controller->execute());
    }
}
