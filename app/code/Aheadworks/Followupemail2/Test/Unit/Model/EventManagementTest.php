<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Test\Unit\Model;

use Aheadworks\Followupemail2\Api\Data\EmailContentInterface;
use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\EmailSearchResultsInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueSearchResultsInterface;
use Aheadworks\Followupemail2\Api\Data\EventSearchResultsInterface;
use Aheadworks\Followupemail2\Model\Config;
use Aheadworks\Followupemail2\Model\EventManagement;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\EmailRepositoryInterface;
use Aheadworks\Followupemail2\Api\EventQueueRepositoryInterface;
use Aheadworks\Followupemail2\Model\Unsubscribe\Service as UnsubscribeService;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Followupemail2\Model\EventManagement
 */
class EventManagementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var EventManagement
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var EventRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventRepositoryMock;

    /**
     * @var EmailRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailRepositoryMock;

    /**
     * @var EventQueueRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventQueueRepositoryMock;

    /**
     * @var UnsubscribeService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $unsubscribeServiceMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventRepositoryMock = $this->getMockBuilder(EventRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->emailRepositoryMock = $this->getMockBuilder(EmailRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->eventQueueRepositoryMock = $this->getMockBuilder(EventQueueRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->unsubscribeServiceMock = $this->getMockBuilder(UnsubscribeService::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['create', 'addFilter'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            EventManagement::class,
            [
                'config' => $this->configMock,
                'eventRepository' => $this->eventRepositoryMock,
                'emailRepository' => $this->emailRepositoryMock,
                'eventQueueRepository' => $this->eventQueueRepositoryMock,
                'unsubscribeService' => $this->unsubscribeServiceMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock
            ]
        );
    }

    /**
     * Test duplicateEventEmails method
     */
    public function testDuplicateEventEmails()
    {
        $srcEventId = 1;
        $destEventId = 2;
        $srcEmailId = 10;

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->with(EmailInterface::EVENT_ID, $srcEventId, 'eq')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $srcEmailMock = $this->getMockBuilder(EmailInterface::class)
            ->getMockForAbstractClass();
        $srcEmailMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($srcEmailId);

        $emailContentMock = $this->getMockBuilder(EmailContentInterface::class)
            ->getMockForAbstractClass();
        $emailContentMock->expects($this->once())
            ->method('setId')
            ->with(null)
            ->willReturnSelf();
        $emailContentMock->expects($this->once())
            ->method('setEmailId')
            ->with(null)
            ->willReturnSelf();
        $destEmailMock = $this->getMockBuilder(EmailInterface::class)
            ->getMockForAbstractClass();
        $destEmailMock->expects($this->atLeastOnce())
            ->method('getContent')
            ->willReturn([$emailContentMock]);
        $destEmailMock->expects($this->atLeastOnce())
            ->method('setId')
            ->with(null)
            ->willReturnSelf();
        $destEmailMock->expects($this->atLeastOnce())
            ->method('setContent')
            ->with([$emailContentMock])
            ->willReturnSelf();
        $destEmailMock->expects($this->atLeastOnce())
            ->method('setEventId')
            ->with($destEventId)
            ->willReturnSelf();

        $emailSearchResultsMock = $this->getMockBuilder(EmailSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $emailSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$srcEmailMock]);
        $this->emailRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($emailSearchResultsMock);
        $this->emailRepositoryMock->expects($this->once())
            ->method('get')
            ->with($srcEmailId)
            ->willReturn($destEmailMock);
        $this->emailRepositoryMock->expects($this->once())
            ->method('save')
            ->with($destEmailMock)
            ->willReturn($destEmailMock);

        $this->assertTrue($this->model->duplicateEventEmails($srcEventId, $destEventId));
    }

    /**
     * Test getEventsByCampaignId method
     */
    public function testGetEventsByCampaignId()
    {
        $campaignId = 1;

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->with(EventInterface::CAMPAIGN_ID, $campaignId, 'eq')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventMock = $this->getMockBuilder(EventInterface::class)
            ->getMockForAbstractClass();
        $eventSearchResultsMock = $this->getMockBuilder(EventSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventMock]);
        $this->eventRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventSearchResultsMock);

        $this->assertEquals([$eventMock], $this->model->getEventsByCampaignId($campaignId));
    }

    /**
     * Test unsubscribeFromEvent method
     */
    public function testUnsubscribeFromEvent()
    {
        $securityCode = 'AAAABBBBBCCCC';
        $storeId = 1;
        $email = 'test@example.com';
        $eventId = 1;
        $eventData = [
            'email' => $email,
            'store_id' => $storeId
        ];

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->with(EventQueueInterface::SECURITY_CODE, $securityCode, 'eq')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getEventId')
            ->willReturn($eventId);
        $eventQueueItemMock->expects($this->once())
            ->method('getEventData')
            ->willReturn(serialize($eventData));
        $eventQueueSearchResultsMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultsMock);

        $this->configMock->expects($this->once())
            ->method('isTestModeEnabled')
            ->with($storeId)
            ->willReturn(false);

        $this->unsubscribeServiceMock->expects($this->once())
            ->method('unsubscribeFromEvent')
            ->with($eventId, $email, $storeId)
            ->willReturn(true);

        $this->assertTrue($this->model->unsubscribeFromEvent($securityCode));
    }

    /**
     * Test unsubscribeFromEventType method
     */
    public function testUnsubscribeFromEventType()
    {
        $securityCode = 'AAAABBBBBCCCC';
        $storeId = 1;
        $email = 'test@example.com';
        $eventType = EventInterface::TYPE_ABANDONED_CART;
        $eventData = [
            'email' => $email,
            'store_id' => $storeId
        ];

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->with(EventQueueInterface::SECURITY_CODE, $securityCode, 'eq')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getEventType')
            ->willReturn($eventType);
        $eventQueueItemMock->expects($this->once())
            ->method('getEventData')
            ->willReturn(serialize($eventData));
        $eventQueueSearchResultsMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultsMock);

        $this->configMock->expects($this->once())
            ->method('isTestModeEnabled')
            ->with($storeId)
            ->willReturn(false);

        $this->unsubscribeServiceMock->expects($this->once())
            ->method('unsubscribeFromEventType')
            ->with($eventType, $email, $storeId)
            ->willReturn(true);

        $this->assertTrue($this->model->unsubscribeFromEventType($securityCode));
    }

    /**
     * Test unsubscribeFromAll method
     */
    public function testUnsubscribeFromAll()
    {
        $securityCode = 'AAAABBBBBCCCC';
        $storeId = 1;
        $email = 'test@example.com';
        $eventData = [
            'email' => $email,
            'store_id' => $storeId
        ];

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilter')
            ->with(EventQueueInterface::SECURITY_CODE, $securityCode, 'eq')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $eventQueueItemMock = $this->getMockBuilder(EventQueueInterface::class)
            ->getMockForAbstractClass();
        $eventQueueItemMock->expects($this->once())
            ->method('getEventData')
            ->willReturn(serialize($eventData));
        $eventQueueSearchResultsMock = $this->getMockBuilder(EventQueueSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $eventQueueSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$eventQueueItemMock]);
        $this->eventQueueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($eventQueueSearchResultsMock);

        $this->configMock->expects($this->once())
            ->method('isTestModeEnabled')
            ->with($storeId)
            ->willReturn(false);

        $this->unsubscribeServiceMock->expects($this->once())
            ->method('unsubscribeFromAll')
            ->with($email, $storeId)
            ->willReturn(true);

        $this->assertTrue($this->model->unsubscribeFromAll($securityCode));
    }
}
