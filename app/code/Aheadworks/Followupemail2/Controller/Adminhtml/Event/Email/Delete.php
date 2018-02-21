<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Controller\Adminhtml\Event\Email;

use Aheadworks\Followupemail2\Api\CampaignManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\EmailRepositoryInterface;
use Aheadworks\Followupemail2\Api\Data\StatisticsInterface;
use Aheadworks\Followupemail2\Api\StatisticsManagementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class Delete
 * @package Aheadworks\Followupemail2\Controller\Adminhtml\Event\Email
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Followupemail2::campaigns_actions';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CampaignManagementInterface
     */
    private $campaignManagement;

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * EmailRepositoryInterface
     */
    private $emailRepository;

    /**
     * @var StatisticsManagementInterface
     */
    private $statisticsManagement;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CampaignManagementInterface $campaignManagement
     * @param EventRepositoryInterface $eventRepository
     * @param EmailRepositoryInterface $emailRepository
     * @param StatisticsManagementInterface $statisticsManagement
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CampaignManagementInterface $campaignManagement,
        EventRepositoryInterface $eventRepository,
        EmailRepositoryInterface $emailRepository,
        StatisticsManagementInterface $statisticsManagement,
        DataObjectProcessor $dataObjectProcessor
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->campaignManagement = $campaignManagement;
        $this->eventRepository = $eventRepository;
        $this->emailRepository = $emailRepository;
        $this->statisticsManagement = $statisticsManagement;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->emailRepository->deleteById($id);

                $campaignId = $this->getCampaignId($id);

                $eventsCount = 0;
                $emailsCount = 0;
                $campaignStatsData = [];
                if ($campaignId) {
                    $eventsCount = $this->campaignManagement->getEventsCount($campaignId);
                    $emailsCount = $this->campaignManagement->getEmailsCount($campaignId);

                    $campaignStats = $this->statisticsManagement->getByCampaignId($campaignId);
                    $campaignStatsData = $this->dataObjectProcessor->buildOutputDataArray(
                        $campaignStats,
                        StatisticsInterface::class
                    );
                }

                $result = [
                    'error'     => false,
                    'message'   => __('Success.'),
                    'events_count' => $eventsCount,
                    'emails_count' => $emailsCount,
                    'campaign_stats' => $campaignStatsData
                ];
            } catch (\Exception $e) {
                $result = [
                    'error'     => true,
                    'message'   => __($e->getMessage())
                ];
            }
        } else {
            $result = [
                'error'     => true,
                'message'   => __('Email Id is not specified!')
            ];
        }
        return $resultJson->setData($result);
    }

    /**
     * Get campaign id
     *
     * @param int $emailId
     * @return int|null
     */
    private function getCampaignId($emailId)
    {
        $campaignId = null;
        try {
            /** @var EmailInterface $email */
            $email = $this->emailRepository->get($emailId);

            /** @var EventInterface $event */
            $event = $this->eventRepository->get($email->getEventId());

            $campaignId = $event->getCampaignId();
        } catch (NoSuchEntityException $e) {
        }
        return $campaignId;
    }
}
