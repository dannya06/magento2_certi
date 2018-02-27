<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Model;

use Aheadworks\Followupemail2\Api\Data\EmailContentInterface;
use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueInterface;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\EmailRepositoryInterface;
use Aheadworks\Followupemail2\Api\Data\QueueInterface;
use Aheadworks\Followupemail2\Api\Data\QueueInterfaceFactory;
use Aheadworks\Followupemail2\Api\QueueManagementInterface;
use Aheadworks\Followupemail2\Api\QueueRepositoryInterface;
use Aheadworks\Followupemail2\Api\Data\QueueSearchResultsInterface;
use Aheadworks\Followupemail2\Api\Data\PreviewInterface;
use Aheadworks\Followupemail2\Api\Data\PreviewInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Store\Model\Store;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class QueueManagement
 * @package Aheadworks\Followupemail2\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QueueManagement implements QueueManagementInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var EmailRepositoryInterface
     */
    private $emailRepository;

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @var QueueInterfaceFactory
     */
    private $queueFactory;

    /**
     * @var PreviewInterfaceFactory
     */
    private $previewFactory;

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var AppEmulation
     */
    private $appEmulation;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param Config $config
     * @param EventRepositoryInterface $eventRepository
     * @param EmailRepositoryInterface $emailRepository
     * @param QueueRepositoryInterface $queueRepository
     * @param QueueInterfaceFactory $queueFactory
     * @param PreviewInterfaceFactory $previewFactory
     * @param Sender $sender
     * @param DateTime $dateTime
     * @param AppEmulation $appEmulation
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Config $config,
        EventRepositoryInterface $eventRepository,
        EmailRepositoryInterface $emailRepository,
        QueueRepositoryInterface $queueRepository,
        QueueInterfaceFactory $queueFactory,
        PreviewInterfaceFactory $previewFactory,
        Sender $sender,
        DateTime $dateTime,
        AppEmulation $appEmulation,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->config = $config;
        $this->eventRepository = $eventRepository;
        $this->emailRepository = $emailRepository;
        $this->queueRepository = $queueRepository;
        $this->queueFactory = $queueFactory;
        $this->previewFactory = $previewFactory;
        $this->sender = $sender;
        $this->dateTime = $dateTime;
        $this->appEmulation = $appEmulation;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function send(QueueInterface $queue)
    {
        try {
            $this->appEmulation->startEnvironmentEmulation($queue->getStoreId(), 'frontend', true);
            $queue = $this->sender->sendQueueItem($queue);
            $this->appEmulation->stopEnvironmentEmulation();
            $queue->setStatus(QueueInterface::STATUS_SENT);
            $queue->setSentAt($this->dateTime->date());
            $this->saveQueueItemSafely($queue);

            return true;
        } catch (MailException $e) {
            $this->appEmulation->stopEnvironmentEmulation();
            $queue->setStatus(QueueInterface::STATUS_FAILED);
            $this->saveQueueItemSafely($queue);

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendById($queueId)
    {
        try {
            $queue = $this->queueRepository->get($queueId);
            $result = $this->send($queue);
            return $result;
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreview(QueueInterface $queue)
    {
        /** @var PreviewInterface $preview */
        $preview = $this->previewFactory->create();
        $preview
            ->setStoreId($queue->getStoreId())
            ->setSenderName($queue->getSenderName())
            ->setSenderEmail($queue->getSenderEmail())
            ->setRecipientName($queue->getRecipientName())
            ->setRecipientEmail($queue->getRecipientEmail())
            ->setSubject($queue->getSubject())
            ->setContent($queue->getContent());

        return $preview;
    }

    /**
     * {@inheritdoc}
     */
    public function sendTest(EmailInterface $email, $contentId = null)
    {
        /** @var EventInterface $event */
        $event = $this->eventRepository->get($email->getEventId());
        $storeIds = $event->getStoreIds();
        if (count($storeIds) > 0) {
            $storeId = array_shift($storeIds);
        } else {
            $storeId = Store::DEFAULT_STORE_ID;
        }

        /** @var EmailContentInterface[] $contentData */
        $contentData = $email->getContent();
        if ($contentId !== null) {
            if ($contentId == EmailInterface::CONTENT_VERSION_A) {
                /** @var EmailContentInterface $content */
                $content = $contentData[0];
            } else {
                /** @var EmailContentInterface $content */
                $content = $contentData[1];
            }
        } else {
            /** @var EmailContentInterface $content */
            $content = reset($contentData);
        }

        /** @var QueueInterface $queue */
        $queue = $this->queueFactory->create();
        $queue
            ->setEventId($event->getId())
            ->setEventType($event->getEventType())
            ->setEventEmailId($email->getId())
            ->setEmailContentId($content->getId())
            ->setStoreId($storeId);

        if ($email->getAbTestingMode()) {
            $queue
                ->setContentVersion($contentId);
        }

        try {
            $this->appEmulation->startEnvironmentEmulation($storeId, 'frontend', true);
            $queue = $this->sender->sendTestEmail($queue, $content);
            $this->appEmulation->stopEnvironmentEmulation();

            $date = $this->dateTime->date();
            $queue
                ->setStatus(QueueInterface::STATUS_SENT)
                ->setScheduledAt($date)
                ->setSentAt($date);
            $this->saveQueueItemSafely($queue);
            return true;
        } catch (MailException $e) {
            $this->appEmulation->stopEnvironmentEmulation();
            $queue->setStatus(QueueInterface::STATUS_FAILED);
            $this->saveQueueItemSafely($queue);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function schedule(EventQueueInterface $eventQueueItem, EmailInterface $email, $eventQueueEmailId)
    {
        $eventData = unserialize($eventQueueItem->getEventData());
        $storeId = $eventData['store_id'];

        /** @var EmailContentInterface[] $contentData */
        $contentData = $email->getContent();
        if ($email->getAbTestingMode()) {
            $contentVersion = $email->getAbEmailContent();
            if ($contentVersion) {
                if ($contentVersion == EmailInterface::CONTENT_VERSION_A) {
                    $contentVersion = EmailInterface::CONTENT_VERSION_B;
                    $content = $contentData[1];
                } else {
                    $contentVersion = EmailInterface::CONTENT_VERSION_A;
                    $content = $contentData[0];
                }
            } else {
                $contentVersion = EmailInterface::CONTENT_VERSION_A;
                $content = $contentData[0];
            }
        } else {
            if ($email->getPrimaryEmailContent() == EmailInterface::CONTENT_VERSION_A) {
                $content = $contentData[0];
            } else {
                $content = $contentData[1];
            }
        }

        try {
            $this->appEmulation->startEnvironmentEmulation($storeId, 'frontend', true);
            $renderedEmail = $this->sender->renderEventQueueItem($eventQueueItem, $content);
            $this->appEmulation->stopEnvironmentEmulation();

            $date = $this->dateTime->date();

            $senderEmail = $content->getSenderEmail() ?
                $content->getSenderEmail() :
                $this->config->getSenderEmail($storeId);
            $senderName = $content->getSenderName() ?
                $content->getSenderName() :
                $this->config->getSenderName($storeId);

            /** @var QueueInterface $queue */
            $queue = $this->queueFactory->create();
            $queue
                ->setEventId($eventQueueItem->getEventId())
                ->setEventType($eventQueueItem->getEventType())
                ->setEventEmailId($email->getId())
                ->setEmailContentId($content->getId())
                ->setEventQueueEmailId($eventQueueEmailId)
                ->setSenderEmail($senderEmail)
                ->setSenderName($senderName)
                ->setRecipientEmail($renderedEmail['recipient_email'])
                ->setRecipientName($renderedEmail['recipient_name'])
                ->setSubject($renderedEmail['subject'])
                ->setContent($renderedEmail['content'])
                ->setStoreId($storeId)
                ->setStatus(QueueInterface::STATUS_PENDING)
                ->setScheduledAt($date);

            if ($email->getAbTestingMode()) {
                $queue->setContentVersion($contentVersion);
                $email->setAbEmailContent($contentVersion);
            }
            $this->saveQueueItemSafely($queue);
            if ($email->getAbTestingMode()) {
                $this->emailRepository->save($email);
            }
        } catch (\Exception $e) {
            $this->appEmulation->stopEnvironmentEmulation();
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function sendScheduledEmails($emailsCount)
    {
        $now = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, $this->dateTime->timestamp());

        $this->searchCriteriaBuilder
            ->addFilter(QueueInterface::STATUS, QueueInterface::STATUS_PENDING, 'eq')
            ->addFilter(QueueInterface::SCHEDULED_AT, $now, 'lteq');

        /** @var QueueSearchResultsInterface $result */
        $result = $this->queueRepository->getList(
            $this->searchCriteriaBuilder->create()
        );

        /** @var QueueInterface $queueItem */
        foreach ($result->getItems() as $queueItem) {
            try {
                $this->send($queueItem);
            } catch (\Exception $e) {
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clearQueue($keepForDays)
    {
        if ($keepForDays) {
            $expirationDate = date(
                \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT,
                strtotime("-" . $keepForDays . " days")
            );

            $this->searchCriteriaBuilder
                ->addFilter(QueueInterface::SCHEDULED_AT, $expirationDate, 'lteq');

            /** @var QueueSearchResultsInterface $result */
            $result = $this->queueRepository->getList(
                $this->searchCriteriaBuilder->create()
            );

            /** @var QueueInterface $queueItem */
            foreach ($result->getItems() as $queueItem) {
                $this->queueRepository->delete($queueItem);
            }
            return true;
        }
        return false;
    }

    /**
     * Safely saving queue item with exception handling
     *
     * @param QueueInterface $queueItem
     * @return QueueInterface
     * @throws LocalizedException
     */
    private function saveQueueItemSafely($queueItem)
    {
        try {
            return $this->queueRepository->save($queueItem);
        } catch (CouldNotSaveException $exception) {
            throw new LocalizedException(__('The email failed to save.'));
        }
    }
}
