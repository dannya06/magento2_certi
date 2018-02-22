<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Test\Unit\Model\Event;

use Aheadworks\Followupemail2\Api\Data\EventHistoryInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Model\Event\QueueManagement;
use Aheadworks\Followupemail2\Api\CampaignManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueInterfaceFactory;
use Aheadworks\Followupemail2\Api\Data\EventQueueSearchResultsInterface;
use Aheadworks\Followupemail2\Api\EventQueueRepositoryInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueEmailInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueEmailInterfaceFactory;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\EventManagementInterface;
use Aheadworks\Followupemail2\Api\EmailManagementInterface;
use Aheadworks\Followupemail2\Api\QueueManagementInterface;
use Aheadworks\Followupemail2\Model\Event\Queue\CodeGenerator;
use Aheadworks\Followupemail2\Model\Unsubscribe\Service as UnsubscribeService;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Test for \Aheadworks\Followupemail2\Model\Event\QueueManagement
 */
class QueueManagementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QueueManagement
     */
    private $model;

    /**
     * @var CampaignManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $campaignManagementMock;

    /**
     * @var EventQueueInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventQueueFactoryMock;

    /**
     * @var EventQueueRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventQueueRepositoryMock;

    /**
     * @var EventQueueEmailInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventQueueEmailFactoryMock;

    /**
     * @var EventRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventRepositoryMock;

    /**
     * @var EventManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventManagementMock;

    /**
     * @var EmailManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailManagementMock;

    /**
     * @var QueueManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueManagementMock;

    /**
     * @var CodeGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $codeGeneratorMock;

    /**
     * @var UnsubscribeService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $unsubscribeServiceMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->campaignManagementMock = $this->getMockBuilder(CampaignManagementInterface::class)
            ->getMockForAbstractClass();
        $this->eventQueueFactoryMock = $this->getMockBuilder(EventQueueInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventQueueRepositoryMock = $this->getMockBuilder(EventQueueRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->eventQueueEmailFactoryMock = $this->getMockBuilder(EventQueueEmailInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventRepositoryMock = $this->getMockBuilder(EventRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->eventManagementMock = $this->getMockBuilder(EventManagementInterface::class)
            ->getMockForAbstractClass();
        $this->emailManagementMock = $this->getMockBuilder(EmailManagementInterface::class)
            ->getMockForAbstractClass();
        $this->queueManagementMock = $this->getMockBuilder(QueueManagementInterface::class)
            ->getMockForAbstractClass();
        $this->codeGeneratorMock = $this->getMockBuilder(CodeGenerator::class)
            ->setMethods(['getCode'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->unsubscribeServiceMock = $this->getMockBuilder(UnsubscribeService::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['create', 'addFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dateTimeMock = $this->getMockBuilder(DateTime::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            QueueManagement::class,
            [
                'campaignManagement' => $this->campaignManagementMock,
                'eventQueueFactory' => $this->eventQueueFactoryMock,
                'eventQueueRepository' => $this->eventQueueRepositoryMock,
                'eventQueueEmailInterfaceFactory' => $this->eventQueueEmailFactoryMock,
                'eventRepository' => $this->eventRepositoryMock,
                'eventManagement' => $this->eventManagementMock,
                'emailManagement' => $this->emailManagementMock,
                'queueManagement' => $this->queueManagementMock,
                'codeGenerator' => $this->codeGeneratorMock,
                'unsubscribeService' => $this->unsubscribeServiceMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'dateTime' => $this->dateTimeMock,
            ]
        );
    }

    /**
     * Test cancelEvents method
     */
    public function testCancelEvents()
    {
        $eventCode = EventInterface::TYPE_ABANDONED_CART;
        $referenceId = 1;
        $eventQueueId = 10;

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->withConsecutive(
                [EventQueueInterface::EVENT_TYPE, $eventCode, 'eq'],
                [EventQueueInterface::REFERENCE_ID, $referenceId, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($eventQueueId);
        $eventQueueItemMock->expects($this->once())
            ->method('getEmails')
            ->willReturn([]);
        $eventQueueSearchResultMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultMock);

        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($eventQueueId)
            ->willReturn($eventQueueItemMock);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($eventQueueItemMock)
            ->willReturn(true);

        $this->assertTrue($this->model->cancelEvents($eventCode, $referenceId));
    }

    /**
     * Test cancelEvents method if some emails already created
     */
    public function testCancelEventsWithEmails()
    {
        $eventCode = EventInterface::TYPE_ABANDONED_CART;
        $referenceId = 1;
        $eventQueueId = 10;

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->withConsecutive(
                [EventQueueInterface::EVENT_TYPE, $eventCode, 'eq'],
                [EventQueueInterface::REFERENCE_ID, $referenceId, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $emailMock = $this->getMockBuilder(EventQueueEmailInterface::class)
            ->getMockForAbstractClass();

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($eventQueueId);
        $eventQueueItemMock->expects($this->once())
            ->method('getEmails')
            ->willReturn([$emailMock]);
        $eventQueueItemMock->expects($this->once())
            ->method('setStatus')
            ->with(EventQueueInterface::STATUS_CANCELLED)
            ->willReturnSelf();
        $eventQueueSearchResultMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultMock);

        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($eventQueueId)
            ->willReturn($eventQueueItemMock);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($eventQueueItemMock)
            ->willReturn($eventQueueItemMock);

        $this->assertTrue($this->model->cancelEvents($eventCode, $referenceId));
    }

    /**
     * Test cancelEventsByCampaignId method
     */
    public function testCancelEventsByCampaignId()
    {
        $campaignId = 1;
        $eventId = 2;
        $eventQueueId = 100;

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventMock->expects($this->once())
            ->method('getId')
            ->willReturn($eventId);
        $this->eventManagementMock->expects($this->once())
            ->method('getEventsByCampaignId')
            ->with($campaignId)
            ->willReturn([$eventMock]);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->withConsecutive(
                [EventQueueInterface::EVENT_ID, [$eventId], 'in'],
                [EventQueueInterface::STATUS, EventQueueInterface::STATUS_PROCESSING, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($eventQueueId);
        $eventQueueItemMock->expects($this->once())
            ->method('getEmails')
            ->willReturn([]);
        $eventQueueSearchResultMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultMock);

        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($eventQueueId)
            ->willReturn($eventQueueItemMock);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($eventQueueItemMock)
            ->willReturn(true);

        $this->assertTrue($this->model->cancelEventsByCampaignId($campaignId));
    }

    /**
     * Test cancelEventsByCampaignId method if some emails already created
     */
    public function testCancelEventsByCampaignIdWithEmails()
    {
        $campaignId = 1;
        $eventId = 2;
        $eventQueueId = 100;

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventMock->expects($this->once())
            ->method('getId')
            ->willReturn($eventId);
        $this->eventManagementMock->expects($this->once())
            ->method('getEventsByCampaignId')
            ->with($campaignId)
            ->willReturn([$eventMock]);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->withConsecutive(
                [EventQueueInterface::EVENT_ID, [$eventId], 'in'],
                [EventQueueInterface::STATUS, EventQueueInterface::STATUS_PROCESSING, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $emailMock = $this->getMockBuilder(EventQueueEmailInterface::class)
            ->getMockForAbstractClass();

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($eventQueueId);
        $eventQueueItemMock->expects($this->once())
            ->method('getEmails')
            ->willReturn([$emailMock]);
        $eventQueueItemMock->expects($this->once())
            ->method('setStatus')
            ->with(EventQueueInterface::STATUS_CANCELLED)
            ->willReturnSelf();
        $eventQueueSearchResultMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultMock);

        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($eventQueueId)
            ->willReturn($eventQueueItemMock);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($eventQueueItemMock)
            ->willReturn($eventQueueItemMock);

        $this->assertTrue($this->model->cancelEventsByCampaignId($campaignId));
    }

    /**
     * Test cancelEventsByEventId method
     */
    public function testCancelEventsByEventId()
    {
        $eventId = 1;
        $eventQueueId = 100;

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->withConsecutive(
                [EventQueueInterface::EVENT_ID, $eventId, 'eq'],
                [EventQueueInterface::STATUS, EventQueueInterface::STATUS_PROCESSING, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($eventQueueId);
        $eventQueueItemMock->expects($this->once())
            ->method('getEmails')
            ->willReturn([]);
        $eventQueueSearchResultMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultMock);

        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($eventQueueId)
            ->willReturn($eventQueueItemMock);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($eventQueueItemMock)
            ->willReturn(true);

        $this->assertTrue($this->model->cancelEventsByEventId($eventId));
    }

    /**
     * Test cancelEventsByEventId method if some emails already created
     */
    public function testCancelEventsByEventIdWithEmails()
    {
        $eventId = 1;
        $eventQueueId = 100;

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->withConsecutive(
                [EventQueueInterface::EVENT_ID, $eventId, 'eq'],
                [EventQueueInterface::STATUS, EventQueueInterface::STATUS_PROCESSING, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $emailMock = $this->getMockBuilder(EventQueueEmailInterface::class)
            ->getMockForAbstractClass();

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($eventQueueId);
        $eventQueueItemMock->expects($this->once())
            ->method('getEmails')
            ->willReturn([$emailMock]);
        $eventQueueItemMock->expects($this->once())
            ->method('setStatus')
            ->with(EventQueueInterface::STATUS_CANCELLED)
            ->willReturnSelf();
        $eventQueueSearchResultMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultMock);

        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($eventQueueId)
            ->willReturn($eventQueueItemMock);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($eventQueueItemMock)
            ->willReturn($eventQueueItemMock);

        $this->assertTrue($this->model->cancelEventsByEventId($eventId));
    }

    /**
     * Test add method
     */
    public function testAdd()
    {
        $storeId = 1;
        $eventId = 2;
        $eventType = EventInterface::TYPE_ABANDONED_CART;
        $email = 'test@example.com';
        $eventData = serialize(
            [
                'store_id' => $storeId,
                'email' => $email,
            ]
        );
        $referenceId = 10;
        $newEventStatus = EventQueueInterface::STATUS_PROCESSING;
        $securityCode = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabc0';

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($eventId);

        $eventHistoryItemMock = $this->getMockBuilder(EventHistoryInterface::class)
            ->getMockForAbstractClass();
        $eventHistoryItemMock->expects($this->atLeastOnce())
            ->method('getEventData')
            ->willReturn($eventData);
        $eventHistoryItemMock->expects($this->atLeastOnce())
            ->method('getReferenceId')
            ->willReturn($referenceId);
        $eventHistoryItemMock->expects($this->once())
            ->method('getEventType')
            ->willReturn($eventType);

        $this->unsubscribeServiceMock->expects($this->once())
            ->method('isUnsubscribed')
            ->with($eventId, $email, $storeId)
            ->willReturn(false);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->withConsecutive(
                [EventQueueInterface::EVENT_ID, $eventId, 'eq'],
                [EventQueueInterface::REFERENCE_ID, $referenceId, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getEmails')
            ->willReturn([]);
        $eventQueueSearchResultMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultMock);

        $newEventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $newEventQueueItemMock->expects($this->once())
            ->method('setEventId')
            ->with($eventId)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setReferenceId')
            ->with($referenceId)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setEventType')
            ->with($eventType)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setEventData')
            ->with($eventData)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setSecurityCode')
            ->with($securityCode)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setStatus')
            ->with($newEventStatus)
            ->willReturnSelf();
        $this->eventQueueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newEventQueueItemMock);

        $this->codeGeneratorMock->expects($this->once())
            ->method('getCode')
            ->willReturn($securityCode);

        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($newEventQueueItemMock)
            ->willReturn($newEventQueueItemMock);

        $this->assertEquals($newEventQueueItemMock, $this->model->add($eventMock, $eventHistoryItemMock));
    }

    /**
     * Test add method if an error occurs on save event queue item
     */
    public function testAddErrorOnSave()
    {
        $storeId = 1;
        $eventId = 2;
        $eventType = EventInterface::TYPE_ABANDONED_CART;
        $email = 'test@example.com';
        $eventData = serialize(
            [
                'store_id' => $storeId,
                'email' => $email,
            ]
        );
        $referenceId = 10;
        $newEventStatus = EventQueueInterface::STATUS_PROCESSING;
        $securityCode = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabc0';

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($eventId);

        $eventHistoryItemMock = $this->getMockBuilder(EventHistoryInterface::class)
            ->getMockForAbstractClass();
        $eventHistoryItemMock->expects($this->atLeastOnce())
            ->method('getEventData')
            ->willReturn($eventData);
        $eventHistoryItemMock->expects($this->atLeastOnce())
            ->method('getReferenceId')
            ->willReturn($referenceId);
        $eventHistoryItemMock->expects($this->once())
            ->method('getEventType')
            ->willReturn($eventType);

        $this->unsubscribeServiceMock->expects($this->once())
            ->method('isUnsubscribed')
            ->with($eventId, $email, $storeId)
            ->willReturn(false);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->withConsecutive(
                [EventQueueInterface::EVENT_ID, $eventId, 'eq'],
                [EventQueueInterface::REFERENCE_ID, $referenceId, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getEmails')
            ->willReturn([]);
        $eventQueueSearchResultMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultMock);

        $newEventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $newEventQueueItemMock->expects($this->once())
            ->method('setEventId')
            ->with($eventId)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setReferenceId')
            ->with($referenceId)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setEventType')
            ->with($eventType)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setEventData')
            ->with($eventData)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setSecurityCode')
            ->with($securityCode)
            ->willReturnSelf();
        $newEventQueueItemMock->expects($this->once())
            ->method('setStatus')
            ->with($newEventStatus)
            ->willReturnSelf();
        $this->eventQueueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newEventQueueItemMock);

        $this->codeGeneratorMock->expects($this->once())
            ->method('getCode')
            ->willReturn($securityCode);

        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($newEventQueueItemMock)
            ->willThrowException(new CouldNotSaveException(__('Unknown error!')));

        $this->assertFalse($this->model->add($eventMock, $eventHistoryItemMock));
    }

    /**
     * Test add method if there are sent emails
     */
    public function testAddIfHaveAlreadySentEmails()
    {
        $storeId = 1;
        $eventId = 2;
        $email = 'test@example.com';
        $eventData = serialize(
            [
                'store_id' => $storeId,
                'email' => $email,
            ]
        );
        $referenceId = 10;

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($eventId);

        $eventHistoryItemMock = $this->getMockBuilder(EventHistoryInterface::class)
            ->getMockForAbstractClass();
        $eventHistoryItemMock->expects($this->once())
            ->method('getEventData')
            ->willReturn($eventData);
        $eventHistoryItemMock->expects($this->once())
            ->method('getReferenceId')
            ->willReturn($referenceId);

        $this->unsubscribeServiceMock->expects($this->once())
            ->method('isUnsubscribed')
            ->with($eventId, $email, $storeId)
            ->willReturn(false);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->withConsecutive(
                [EventQueueInterface::EVENT_ID, $eventId, 'eq'],
                [EventQueueInterface::REFERENCE_ID, $referenceId, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventQueueEmailMock = $this->getMockBuilder(EventQueueEmailInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getEmails')
            ->willReturn([$eventQueueEmailMock]);
        $eventQueueSearchResultMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultMock);

        $this->assertFalse($this->model->add($eventMock, $eventHistoryItemMock));
    }

    /**
     * Test add method if the email is unsubscribed
     */
    public function testAddEmailUnsubscribed()
    {
        $storeId = 1;
        $eventId = 2;
        $email = 'test@example.com';
        $eventData = serialize(
            [
                'store_id' => $storeId,
                'email' => $email,
            ]
        );

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($eventId);

        $eventHistoryItemMock = $this->getMockBuilder(EventHistoryInterface::class)
            ->getMockForAbstractClass();
        $eventHistoryItemMock->expects($this->once())
            ->method('getEventData')
            ->willReturn($eventData);

        $this->unsubscribeServiceMock->expects($this->once())
            ->method('isUnsubscribed')
            ->with($eventId, $email, $storeId)
            ->willReturn(true);

        $this->assertFalse($this->model->add($eventMock, $eventHistoryItemMock));
    }
}
