<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Model\Event\Plugin;

use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Model\Config;
use Aheadworks\Followupemail2\Api\EventHistoryManagementInterface;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;

/**
 * Class Customer
 * @package Aheadworks\Followupemail2\Model\Plugin
 * @codeCoverageIgnore
 */
class Customer
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
     * @param EventHistoryManagementInterface $eventHistoryManagement
     * @param Config $config
     */
    public function __construct(
        EventHistoryManagementInterface $eventHistoryManagement,
        Config $config
    ) {
        $this->eventHistoryManagement = $eventHistoryManagement;
        $this->config = $config;
    }

    /**
     * Add customer to event history
     *
     * @param CustomerResource $subject,
     * @param \Closure $proceed
     * @param \Magento\Customer\Model\Customer $customer
     * @return CustomerResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        CustomerResource $subject,
        \Closure $proceed,
        \Magento\Customer\Model\Customer $customer
    ) {
        $isNew = $customer->isObjectNew();

        $result = $proceed($customer);

        if ($this->config->isEnabled() &&
            $isNew &&
            $customer->getWebsiteId()
        ) {
            $customerData = array_merge($customer->getData(), [
                'customer_name' => $customer->getName(),
                'customer_group_id' => $customer->getGroupId()
            ]);
            unset($customerData['password_hash']);
            $this->eventHistoryManagement->addEvent(EventInterface::TYPE_CUSTOMER_REGISTRATION, $customerData);
        }

        return $result;
    }
}
