<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Model\Event\Plugin;

use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Model\Config;
use Aheadworks\Followupemail2\Api\EventHistoryManagementInterface;
use Magento\Newsletter\Model\Subscriber\Interceptor as SubscriberInterceptor;
use Magento\Newsletter\Model\Subscriber as SubscriberModel;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Subscriber
 * @package Aheadworks\Followupemail2\Model\Plugin
 * @codeCoverageIgnore
 */
class Subscriber
{
    /**
     * @var EventHistoryManagementInterface
     */
    private $eventHistoryManagement;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @param EventHistoryManagementInterface $eventHistoryManagement
     * @param Config $config
     * @param CustomerRepositoryInterface $customerRepository
     * @param GroupManagementInterface $groupManagement
     */
    public function __construct(
        EventHistoryManagementInterface $eventHistoryManagement,
        Config $config,
        CustomerRepositoryInterface $customerRepository,
        GroupManagementInterface $groupManagement
    ) {
        $this->eventHistoryManagement = $eventHistoryManagement;
        $this->config = $config;
        $this->customerRepository = $customerRepository;
        $this->groupManagement = $groupManagement;
    }

    /**
     * Add subscriber to event history
     *
     * @param SubscriberInterceptor $interceptor
     * @param SubscriberModel $subscriber
     * @return SubscriberModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterAfterSave(
        SubscriberInterceptor $interceptor,
        SubscriberModel $subscriber
    ) {
        if ($this->config->isEnabled()) {
            $customerName = '';
            /** @var GroupInterface $notLoggedInGroup */
            $notLoggedInGroup = $this->groupManagement->getNotLoggedInGroup();
            $customerGroupId = $notLoggedInGroup->getId();
            if ($subscriber->getCustomerId()) {
                try {
                    /** @var CustomerInterface $customer */
                    $customer = $this->customerRepository->getById($subscriber->getCustomerId());
                    $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
                    $customerGroupId = $customer->getGroupId();
                } catch (NoSuchEntityException $e) {
                }
            }

            $subscriberData = array_merge($subscriber->getData(), [
                'email'             => $subscriber->getSubscriberEmail(),
                'customer_name'     => $customerName,
                'customer_group_id' => $customerGroupId

            ]);
            $this->eventHistoryManagement->addEvent(EventInterface::TYPE_NEWSLETTER_SUBSCRIPTION, $subscriberData);
        }
        return $subscriber;
    }
}
