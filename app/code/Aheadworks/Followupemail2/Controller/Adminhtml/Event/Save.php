<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Controller\Adminhtml\Event;

use Aheadworks\Followupemail2\Api\CampaignManagementInterface;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\EventManagementInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterfaceFactory;
use Aheadworks\Followupemail2\Model\Event\CartConditionConverter;
use Aheadworks\Followupemail2\Model\Event\LifetimeConditionConverter;
use Aheadworks\Followupemail2\Ui\DataProvider\Event\ManageFormProcessor;
use Aheadworks\Followupemail2\Model\Event\TypeInterface;
use Aheadworks\Followupemail2\Model\Event\TypePool as EventTypePool;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class Save
 * @package Aheadworks\Followupemail2\Controller\Adminhtml\Event
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
     * EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var EventManagementInterface
     */
    private $eventManagement;

    /**
     * @var EventInterfaceFactory
     */
    private $eventFactory;

    /**
     * @var ManageFormProcessor
     */
    private $manageFormProcessor;

    /**
     * @var EventTypePool
     */
    private $eventTypePool;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var CartConditionConverter
     */
    private $cartConditionConverter;

    /**
     * @var LifetimeConditionConverter
     */
    private $lifetimeConditionConverter;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CampaignManagementInterface $campaignManagement
     * @param EventRepositoryInterface $eventRepository
     * @param EventManagementInterface $eventManagement
     * @param EventInterfaceFactory $eventFactory
     * @param ManageFormProcessor $manageFormProcessor
     * @param EventTypePool $eventTypePool
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CartConditionConverter $cartConditionConverter
     * @param LifetimeConditionConverter $lifetimeConditionConverter
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CampaignManagementInterface $campaignManagement,
        EventRepositoryInterface $eventRepository,
        EventManagementInterface $eventManagement,
        EventInterfaceFactory $eventFactory,
        ManageFormProcessor $manageFormProcessor,
        EventTypePool $eventTypePool,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        CartConditionConverter $cartConditionConverter,
        LifetimeConditionConverter $lifetimeConditionConverter
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->campaignManagement = $campaignManagement;
        $this->eventRepository = $eventRepository;
        $this->eventManagement = $eventManagement;
        $this->eventFactory = $eventFactory;
        $this->manageFormProcessor = $manageFormProcessor;
        $this->eventTypePool = $eventTypePool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->cartConditionConverter = $cartConditionConverter;
        $this->lifetimeConditionConverter = $lifetimeConditionConverter;
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

                /** @var EventInterface $eventDataObject */
                $eventDataObject = $id
                    ? $this->eventRepository->get($id)
                    : $this->eventFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $eventDataObject,
                    $preparedData,
                    EventInterface::class
                );

                $eventDataObject = $this->eventRepository->save($eventDataObject);

                if (isset($data['duplicate_id']) && $data['duplicate_id']) {
                    $this->eventManagement->duplicateEventEmails(
                        $data['duplicate_id'],
                        $eventDataObject->getId()
                    );
                }

                $eventData = $this->dataObjectProcessor->buildOutputDataArray(
                    $eventDataObject,
                    EventInterface::class
                );
                /** @var TypeInterface $eventType */
                $eventType = $this->eventTypePool->getType($eventDataObject->getEventType());
                $eventData['event_type_label'] = $eventType->getTitle();
                $eventData['emails'] = $this->manageFormProcessor->getEventEmailsData($eventDataObject->getId());
                $eventData['totals'] = $this->manageFormProcessor->getEventTotals($eventDataObject->getId());

                $result = [
                    'error'     => false,
                    'message'   => __('Success.'),
                    'event'     => $eventData,
                    'create'    => $id ? false : true,
                    'events_count' => $this->campaignManagement->getEventsCount($eventDataObject->getCampaignId()),
                    'emails_count' => $this->campaignManagement->getEmailsCount($eventDataObject->getCampaignId()),
                ];
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
        $preparedData = [];
        $lifetimeConditions = [];
        foreach ($data as $key => $value) {
            if ($key == EventInterface::CART_CONDITIONS && !isset($data['rule'])) {
                $preparedData[$key] = $value;
            } elseif ($key == 'rule') {
                $preparedData[EventInterface::CART_CONDITIONS] =
                    $this->cartConditionConverter->getConditionsPrepared($data['rule']);
            } elseif ($key == 'lifetime_conditions' ||
                $key == 'lifetime_value' ||
                $key == 'lifetime_from' ||
                $key == 'lifetime_to'
            ) {
                $lifetimeConditions[$key] = $value;
            } elseif ($key == 'customer_groups'
                || $key == 'product_type_ids'
            ) {
                $allFound = false;
                foreach ($value as $groupValue) {
                    if ($groupValue == 'all') {
                        $allFound = true;
                        $preparedData[$key] = ['all'];
                        break;
                    }
                }
                if (!$allFound) {
                    $preparedData[$key] = $value;
                }
            } elseif ($key == 'store_ids') {
                $allStoresFound = false;
                foreach ($value as $storeValue) {
                    if ($storeValue == 0) {
                        $allStoresFound = true;
                        $preparedData[$key] = [0];
                        break;
                    }
                }
                if (!$allStoresFound) {
                    $preparedData[$key] = $value;
                }
            } else {
                $preparedData[$key] = $value;
            }
        }

        if (count($lifetimeConditions) > 0) {
            $preparedData[EventInterface::LIFETIME_CONDITIONS] =
                $this->lifetimeConditionConverter->getConditionsSerialized($lifetimeConditions);
        }

        if (!isset($preparedData[EventInterface::CART_CONDITIONS])) {
            $preparedData[EventInterface::CART_CONDITIONS] = '';
        }
        return $preparedData;
    }
}
