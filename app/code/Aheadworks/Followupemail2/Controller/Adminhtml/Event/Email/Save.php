<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Controller\Adminhtml\Event\Email;

use Aheadworks\Followupemail2\Api\CampaignManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\Data\StatisticsInterface;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\EmailRepositoryInterface;
use Aheadworks\Followupemail2\Api\EmailManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\EmailInterfaceFactory;
use Aheadworks\Followupemail2\Api\Data\EmailContentInterface;
use Aheadworks\Followupemail2\Api\QueueManagementInterface;
use Aheadworks\Followupemail2\Model\Source\Email\Status as EmailStatusSource;
use Aheadworks\Followupemail2\Api\StatisticsManagementInterface;
use Aheadworks\Followupemail2\Ui\DataProvider\Event\ManageFormProcessor;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class Save
 * @package Aheadworks\Followupemail2\Controller\Adminhtml\Event\Email
 */
class Save extends \Magento\Backend\App\Action
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
     * @var EmailManagementInterface
     */
    private $emailManagement;

    /**
     * @var EmailInterfaceFactory
     */
    private $emailFactory;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @var EmailStatusSource
     */
    private $emailStatusSource;

    /**
     * @var ManageFormProcessor
     */
    private $manageFormProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var StatisticsManagementInterface
     */
    private $statisticsManagement;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CampaignManagementInterface $campaignManagement
     * @param EventRepositoryInterface $eventRepository
     * @param EmailRepositoryInterface $emailRepository
     * @param EmailManagementInterface $emailManagement
     * @param EmailInterfaceFactory $emailFactory
     * @param QueueManagementInterface $queueManagement
     * @param EmailStatusSource $emailStatusSource
     * @param ManageFormProcessor $manageFormProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StatisticsManagementInterface $statisticsManagement
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CampaignManagementInterface $campaignManagement,
        EventRepositoryInterface $eventRepository,
        EmailRepositoryInterface $emailRepository,
        EmailManagementInterface $emailManagement,
        EmailInterfaceFactory $emailFactory,
        QueueManagementInterface $queueManagement,
        EmailStatusSource $emailStatusSource,
        ManageFormProcessor $manageFormProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StatisticsManagementInterface $statisticsManagement
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->campaignManagement = $campaignManagement;
        $this->eventRepository = $eventRepository;
        $this->emailRepository = $emailRepository;
        $this->emailManagement = $emailManagement;
        $this->emailFactory = $emailFactory;
        $this->queueManagement = $queueManagement;
        $this->emailStatusSource = $emailStatusSource;
        $this->manageFormProcessor = $manageFormProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->statisticsManagement = $statisticsManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $result = [
            'error'     => true,
            'message'   => __('No data specified!')
        ];
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $id = isset($data['id']) ? $data['id'] : false;

                $preparedData = $this->prepareData($data);

                /** @var EmailInterface $emailDataObject */
                $emailDataObject = $id
                    ? $this->emailRepository->get($id)
                    : $this->emailFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $emailDataObject,
                    $preparedData,
                    EmailInterface::class
                );

                if (!$emailDataObject->getPosition()) {
                    $emailDataObject->setPosition(
                        $this->emailManagement->getNewEmailPosition($emailDataObject->getEventId())
                    );
                }

                $emailDataObject = $this->emailRepository->save($emailDataObject);

                if (isset($data['sendtest']) && $data['sendtest']) {
                    $contentId = isset($data['content_id']) ? $data['content_id'] : null;
                    $testEmailSent = $this->queueManagement->sendTest($emailDataObject, $contentId);
                } else {
                    $testEmailSent = false;
                }

                $emailData = $this->dataObjectProcessor->buildOutputDataArray(
                    $emailDataObject,
                    EmailInterface::class
                );

                /** @var StatisticsInterface $emailStatistics */
                $emailStatistics = $this->emailManagement->getStatistics($emailDataObject);

                $emailData['when'] = $this->manageFormProcessor->getWhen($emailDataObject);
                $emailData['sent'] = $emailStatistics->getSent();
                $emailData['opened'] = $emailStatistics->getOpened();
                $emailData['clicks'] = $emailStatistics->getClicked();
                $emailData['open_rate'] = $emailStatistics->getOpenRate();
                $emailData['click_rate'] = $emailStatistics->getClickRate();
                $emailData['status'] = $this->emailStatusSource->getOptionByValue($emailDataObject->getStatus());
                $emailData['is_email_disabled'] = ($emailDataObject->getStatus() == EmailInterface::STATUS_DISABLED);

                $eventsCount = 0;
                $emailsCount = 0;
                $campaignStatsData = [];
                $campaignId = $this->getCampaignId($emailDataObject->getId());
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
                    'error'         => false,
                    'message'       => __('Success.'),
                    'email'         => $emailData,
                    'totals'        => $this->manageFormProcessor->getEventTotals($emailDataObject->getEventId()),
                    'create'        => $id ? false : true,
                    'continue_edit' => $this->getRequest()->getParam('back') ? $emailDataObject->getId() : false,
                    'events_count'  => $eventsCount,
                    'emails_count'  => $emailsCount,
                    'campaign_stats' => $campaignStatsData
                ];
                if ($testEmailSent) {
                    $result['message'] = __('Email was successfully sent.');
                    $result['send_test'] = true;
                }
            } catch (\Exception $e) {
                $result = [
                    'error'     => true,
                    'message'   => __($e->getMessage())
                ];
            }
        }
        return $resultJson->setData($result);
    }

    /**
     * Prepare data before save
     *
     * @param array $data
     * @return array
     */
    private function prepareData($data)
    {
        if (isset($data['content'])) {
            foreach ($data['content'] as $key => &$value) {
                if (isset($value['use_config'])) {
                    $useConfig = $value['use_config'];
                    if (isset($useConfig[EmailContentInterface::SENDER_NAME])
                        && $useConfig[EmailContentInterface::SENDER_NAME]) {
                        $value[EmailContentInterface::SENDER_NAME] = '';
                    }
                    if (isset($useConfig[EmailContentInterface::SENDER_EMAIL])
                        && $useConfig[EmailContentInterface::SENDER_EMAIL]) {
                        $value[EmailContentInterface::SENDER_EMAIL] = '';
                    }
                    if (isset($useConfig[EmailContentInterface::HEADER_TEMPLATE])
                        && $useConfig[EmailContentInterface::HEADER_TEMPLATE]) {
                        $value[EmailContentInterface::HEADER_TEMPLATE] = '';
                    }
                    if (isset($useConfig[EmailContentInterface::FOOTER_TEMPLATE])
                        && $useConfig[EmailContentInterface::FOOTER_TEMPLATE]) {
                        $value[EmailContentInterface::FOOTER_TEMPLATE] = '';
                    }
                    unset($value['use_config']);
                }
            }
        }

        $preparedData = [];
        foreach ($data as $key => $param) {
            $preparedData[$key] = $param;
        }

        if (isset($preparedData[EmailInterface::AB_TESTING_MODE]) && $preparedData[EmailInterface::AB_TESTING_MODE]) {
            $preparedData[EmailInterface::PRIMARY_EMAIL_CONTENT] = 0;
        }

        return $preparedData;
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
            // do nothing
        }
        return $campaignId;
    }
}
