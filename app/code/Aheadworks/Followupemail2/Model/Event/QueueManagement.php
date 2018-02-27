<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Model\Event;

use Aheadworks\Followupemail2\Api\Data\CampaignInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\Data\EventHistoryInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueSearchResultsInterface;
use Aheadworks\Followupemail2\Api\EventQueueManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueInterfaceFactory;
use Aheadworks\Followupemail2\Api\Data\EventQueueEmailInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueEmailInterfaceFactory;
use Aheadworks\Followupemail2\Api\EventQueueRepositoryInterface;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\EventManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\EmailManagementInterface;
use Aheadworks\Followupemail2\Api\QueueManagementInterface;
use Aheadworks\Followupemail2\Api\CampaignManagementInterface;
use Aheadworks\Followupemail2\Model\Event\Queue\CodeGenerator;
use Aheadworks\Followupemail2\Model\Unsubscribe\Service as UnsubscribeService;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class QueueManagement
 * @package Aheadworks\Followupemail2\Model\Event
 */
class QueueManagement implements EventQueueManagementInterface
{
    /**
     * @var CampaignManagementInterface
     */
    private $campaignManagement;

    /**
     * @var EventQueueInterfaceFactory
     */
    private $eventQueueFactory;

    /**
     * @var EventQueueRepositoryInterface
     */
    private $eventQueueRepository;

    /**
     * @var EventQueueEmailInterfaceFactory
     */
    private $eventQueueEmailInterfaceFactory;

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var EventManagementInterface
     */
    private $eventManagement;

    /**
     * @var EmailManagementInterface
     */
    private $emailManagement;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    /**
     * @var UnsubscribeService
     */
    private $unsubscribeService;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param CampaignManagementInterface $campaignManagement
     * @param EventQueueInterfaceFactory $eventQueueFactory
     * @param EventQueueRepositoryInterface $eventQueueRepository
     * @param EventQueueEmailInterfaceFactory $eventQueueEmailInterfaceFactory
     * @param EventRepositoryInterface $eventRepository
     * @param EventManagementInterface $eventManagement
     * @param EmailManagementInterface $emailManagement
     * @param QueueManagementInterface $queueManagement
     * @param CodeGenerator $codeGenerator
     * @param UnsubscribeService $unsubscribeService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DateTime $dateTime
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CampaignManagementInterface $campaignManagement,
        EventQueueInterfaceFactory $eventQueueFactory,
        EventQueueRepositoryInterface $eventQueueRepository,
        EventQueueEmailInterfaceFactory $eventQueueEmailInterfaceFactory,
        EventRepositoryInterface $eventRepository,
        EventManagementInterface $eventManagement,
        EmailManagementInterface $emailManagement,
        QueueManagementInterface $queueManagement,
        CodeGenerator $codeGenerator,
        UnsubscribeService $unsubscribeService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DateTime $dateTime
    ) {
        $this->campaignManagement = $campaignManagement;
        $this->eventQueueFactory = $eventQueueFactory;
        $this->eventQueueRepository = $eventQueueRepository;
        $this->eventQueueEmailInterfaceFactory = $eventQueueEmailInterfaceFactory;
        $this->eventRepository = $eventRepository;
        $this->eventManagement = $eventManagement;
        $this->emailManagement = $emailManagement;
        $this->queueManagement = $queueManagement;
        $this->codeGenerator = $codeGenerator;
        $this->unsubscribeService = $unsubscribeService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTime = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function cancelEvents($eventCode, $referenceId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(EventQueueInterface::EVENT_TYPE, $eventCode)
            ->addFilter(EventQueueInterface::REFERENCE_ID, $referenceId);

        /** @var EventQueueSearchResultsInterface $result */
        $result = $this->eventQueueRepository->getList(
            $this->searchCriteriaBuilder->create()
        );

        /** @var EventQueueInterface $eventQueueItem */
        foreach ($result->getItems() as $eventQueueItem) {
            $this->cancelEventQueueItem($eventQueueItem->getId());
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function cancelEventsByCampaignId($campaignId)
    {
        /** @var EventInterface[] $events */
        $events = $this->eventManagement->getEventsByCampaignId($campaignId);

        $eventIds = [];
        /** @var EventInterface $event */
        foreach ($events as $event) {
            $eventIds[] = $event->getId();
        }

        if (count($eventIds) > 0) {
            $this->searchCriteriaBuilder
                ->addFilter(EventQueueInterface::EVENT_ID, $eventIds, 'in')
                ->addFilter(EventQueueInterface::STATUS, EventQueueInterface::STATUS_PROCESSING, 'eq');

            /** @var EventQueueSearchResultsInterface $result */
            $result = $this->eventQueueRepository->getList(
                $this->searchCriteriaBuilder->create()
            );

            /** @var EventQueueInterface $eventQueueItem */
            foreach ($result->getItems() as $eventQueueItem) {
                $this->cancelEventQueueItem($eventQueueItem->getId());
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function cancelEventsByEventId($eventId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(EventQueueInterface::EVENT_ID, $eventId, 'eq')
            ->addFilter(EventQueueInterface::STATUS, EventQueueInterface::STATUS_PROCESSING, 'eq');

        /** @var EventQueueSearchResultsInterface $result */
        $result = $this->eventQueueRepository->getList(
            $this->searchCriteriaBuilder->create()
        );

        /** @var EventQueueInterface $eventQueueItem */
        foreach ($result->getItems() as $eventQueueItem) {
            $this->cancelEventQueueItem($eventQueueItem->getId());
        }

        return true;
    }

    /**
     * Cancel event queue item
     *
     * @param int $eventQueueId
     * @return bool
     */
    private function cancelEventQueueItem($eventQueueId)
    {
        try {
            $eventQueueItem = $this->eventQueueRepository->get($eventQueueId);
            if (count($eventQueueItem->getEmails()) > 0) {
                $eventQueueItem->setStatus(EventQueueInterface::STATUS_CANCELLED);
                $this->eventQueueRepository->save($eventQueueItem);
            } else {
                $this->eventQueueRepository->delete($eventQueueItem);
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function add(EventInterface $event, EventHistoryInterface $eventHistoryItem)
    {
        $eventData = unserialize($eventHistoryItem->getEventData());
        $storeId = isset($eventData['store_id']) ? $eventData['store_id'] : 0;
        $email = $eventData['email'];

        if ($this->unsubscribeService->isUnsubscribed($event->getId(), $email, $storeId)) {
            return false;
        }

        $this->searchCriteriaBuilder
            ->addFilter(EventQueueInterface::EVENT_ID, $event->getId())
            ->addFilter(EventQueueInterface::REFERENCE_ID, $eventHistoryItem->getReferenceId());

        /** @var EventQueueSearchResultsInterface $result */
        $result = $this->eventQueueRepository->getList(
            $this->searchCriteriaBuilder->create()
        );

        foreach ($result->getItems() as $eventQueueItem) {
            if (count($eventQueueItem->getEmails()) > 0) {
                // prevent to add duplicate emails
                return false;
            }
        }

        /** @var EventQueueInterface $eventQueueItem */
        $eventQueueItem = $this->eventQueueFactory->create();
        $securityCode = $this->codeGenerator->getCode();
        $eventQueueItem
            ->setEventId($event->getId())
            ->setReferenceId($eventHistoryItem->getReferenceId())
            ->setEventType($eventHistoryItem->getEventType())
            ->setEventData($eventHistoryItem->getEventData())
            ->setSecurityCode($securityCode)
            ->setStatus(EventQueueInterface::STATUS_PROCESSING);

        try {
            $eventQueueItem = $this->eventQueueRepository->save($eventQueueItem);
        } catch (\Exception $e) {
            return false;
        }

        return $eventQueueItem;
    }

    /**
     * {@inheritdoc}
     */
    public function processUnprocessedItems($maxItemsCount)
    {
        $countQueuedEmails = 0;
        $allProcessed = false;
        $page = 1;
        $itemsCount = 0;

        while ($countQueuedEmails < $maxItemsCount && !$allProcessed) {
            $this->searchCriteriaBuilder
                ->addFilter(EventQueueInterface::STATUS, EventQueueInterface::STATUS_PROCESSING)
                ->setPageSize($maxItemsCount)
                ->setCurrentPage($page);

            /** @var EventQueueSearchResultsInterface $result */
            $result = $this->eventQueueRepository->getList(
                $this->searchCriteriaBuilder->create()
            );

            if (count($result->getItems()) == 0) {
                $allProcessed = true;
            }

            /** @var EventQueueInterface $eventQueueItem */
            foreach ($result->getItems() as $eventQueueItem) {
                if ($this->processItem($eventQueueItem)) {
                    $countQueuedEmails++;
                    if ($countQueuedEmails >= $maxItemsCount) {
                        $allProcessed = true;
                        break;
                    }
                }
                $itemsCount++;
                if ($itemsCount >= $result->getTotalCount()) {
                    $allProcessed = true;
                    break;
                }
            }
            $page++;
        }

        return true;
    }

    /**
     * Process event queue item
     *
     * @param EventQueueInterface $eventQueueItem
     * @return bool
     */
    private function processItem($eventQueueItem)
    {
        $lastDate = $eventQueueItem->getCreatedAt();
        /** @var EventInterface $event */
        $event = $this->eventRepository->get($eventQueueItem->getEventId());

        if ($this->isValid($event) && $this->isRecepientNotUnsubscribed($eventQueueItem)) {
            /** @var EventQueueEmailInterface[] $queueEmails */
            $queueEmails = $eventQueueItem->getEmails();
            $processedEmailsCount = count($queueEmails);

            if ($processedEmailsCount > 0) {
                /** @var EventQueueEmailInterface $queueEmail */
                $queueEmail = end($queueEmails);
                if ($queueEmail->getStatus() == EventQueueEmailInterface::STATUS_PENDING) {
                    return false;
                } elseif ($event->getFailedEmailsMode() == EventInterface::FAILED_EMAILS_CANCEL
                    && ($queueEmail->getStatus() == EventQueueEmailInterface::STATUS_FAILED
                        || $queueEmail->getStatus() == EventQueueEmailInterface::STATUS_CANCELLED)
                ) {
                    $eventQueueItem->setStatus(EventQueueInterface::STATUS_CANCELLED);
                    $this->eventQueueRepository->save($eventQueueItem);
                    return false;
                } else {
                    $lastDate = $queueEmail->getUpdatedAt();
                    /** @var EventQueueEmailInterface $queueEmail */
                    $queueEmail = $this->eventQueueEmailInterfaceFactory->create();
                }
            } else {
                /** @var EventQueueEmailInterface $queueEmail */
                $queueEmail = $this->eventQueueEmailInterfaceFactory->create();
            }

            /** @var EmailInterface[] $emails */
            $emails = $this->emailManagement->getEmailsByEventId($event->getId(), true);
            $emailIndexToProcess = $processedEmailsCount;
            if (isset($emails[$emailIndexToProcess])) {
                /** @var EmailInterface $email */
                $email = $emails[$emailIndexToProcess];
                if ($this->isValidToSend($email, $lastDate)) {
                    $queueEmail->setStatus(EventQueueEmailInterface::STATUS_PENDING);
                    $queueEmails[] = $queueEmail;
                    $eventQueueItem->setEmails($queueEmails);
                    $eventQueueItem = $this->eventQueueRepository->save($eventQueueItem);
                    $queueEmails = $eventQueueItem->getEmails();
                    $queueEmail = end($queueEmails);

                    if (!$this->queueManagement->schedule($eventQueueItem, $email, $queueEmail->getId())) {
                        $queueEmail->setStatus(EventQueueEmailInterface::STATUS_FAILED);
                        $this->eventQueueRepository->save($eventQueueItem);
                    }

                    if (!isset($emails[$emailIndexToProcess + 1])) {
                        $eventQueueItem->setStatus(EventQueueInterface::STATUS_FINISHED);
                        $this->eventQueueRepository->save($eventQueueItem);
                    }
                    return true;
                }
            }
        } else {
            $eventQueueItem->setStatus(EventQueueInterface::STATUS_CANCELLED);
            $this->eventQueueRepository->save($eventQueueItem);
        }

        return false;
    }

    /**
     * Is valid to send
     *
     * @param EmailInterface $email
     * @param string $lastEmailDate
     * @return bool
     */
    private function isValidToSend(EmailInterface $email, $lastEmailDate)
    {
        $sendTimestamp = $this->dateTime->timestamp($lastEmailDate) + $this->getDeltaTimestamp($email);
        $currentTimestamp = $this->dateTime->timestamp();

        return $sendTimestamp <= $currentTimestamp;
    }

    /**
     * Get delta timestamp
     * @param EmailInterface $email
     * @return int
     */
    private function getDeltaTimestamp(EmailInterface $email)
    {
        return 60 * ($email->getEmailSendMinutes() +
            60 * ($email->getEmailSendHours() + $email->getEmailSendDays() * 24));
    }

    /**
     * Is valid event
     *
     * @param EventInterface $event
     * @return bool
     */
    private function isValid(EventInterface $event)
    {
        if ($event->getStatus() == EventInterface::STATUS_ENABLED) {
            $eventCampaignId = $event->getCampaignId();
            /** @var CampaignInterface[] $activeCampaigns */
            $activeCampaigns = $this->campaignManagement->getActiveCampaigns();
            foreach ($activeCampaigns as $activeCampaign) {
                if ($activeCampaign->getId() == $eventCampaignId) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if email recipient not unsubscribed
     *
     * @param EventQueueInterface $eventQueueItem
     * @return bool
     */
    private function isRecepientNotUnsubscribed(EventQueueInterface $eventQueueItem)
    {
        $eventData = unserialize($eventQueueItem->getEventData());
        $storeId = isset($eventData['store_id']) ? $eventData['store_id'] : 0;
        $email = $eventData['email'];

        return !$this->unsubscribeService->isUnsubscribed($eventQueueItem->getEventId(), $email, $storeId);
    }
}
