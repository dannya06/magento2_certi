<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Filter;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\UrlInterface;

/**
 * Class CustomerGroup
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
class CustomerGroup
{
    /**
     * @var string
     */
    const SESSION_KEY = 'aw_rep_customer_group_key';

    /**
     * @var []
     */
    private $customerGroupItems;

    /**
     * @var string
     */
    private $currentItemKey;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param RequestInterface $request
     * @param SessionManagerInterface $session
     * @param GroupManagementInterface $groupManagement
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        RequestInterface $request,
        SessionManagerInterface $session,
        GroupManagementInterface $groupManagement,
        UrlInterface $urlBuilder
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->groupManagement = $groupManagement;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve store items
     *
     * @return []
     */
    public function getItems()
    {
        if (!$this->customerGroupItems) {
            /** @var GroupInterface $allGroup */
            $allGroup = $this->groupManagement->getAllCustomersGroup();
            $this->customerGroupItems = [
                $allGroup->getId() => [
                    'title' => __('All Groups'),
                    'url'   => $this->urlBuilder->getUrl(
                        '*/*/*',
                        [
                            '_query' => ['customer_group_id' => $allGroup->getId()],
                            '_current' => true
                        ]
                    )
                ],
            ];

            /** @var GroupInterface $notLoggedInGroup */
            $notLoggedInGroup = $this->groupManagement->getNotLoggedInGroup();
            $this->customerGroupItems[$notLoggedInGroup->getId()] = [
                'title' => $notLoggedInGroup->getCode(),
                'url'   => $this->urlBuilder->getUrl(
                    '*/*/*',
                    [
                        '_query' => ['customer_group_id' => $notLoggedInGroup->getId()],
                        '_current' => true
                    ]
                )
            ];

            /** @var GroupInterface $group */
            foreach ($this->groupManagement->getLoggedInGroups() as $group) {
                $this->customerGroupItems[$group->getId()] = [
                    'title' => $group->getCode(),
                    'url'   => $this->urlBuilder->getUrl(
                        '*/*/*',
                        [
                            '_query' => ['customer_group_id' => $group->getId()],
                            '_current' => true
                        ]
                    )
                ];
            }
        }
        return $this->customerGroupItems;
    }

    /**
     * Retrieve current item key
     *
     * @return string
     */
    public function getCurrentItemKey()
    {
        if ($this->currentItemKey == null) {
            $customerGroupId = $this->request->getParam('customer_group_id');
            if ($customerGroupId != null) {
                $this->currentItemKey = $customerGroupId;
            }
            if ($this->currentItemKey != null) {
                $this->session->setData(self::SESSION_KEY, $this->currentItemKey);
                return $this->currentItemKey;
            }
            $keyFromSession = $this->session->getData(self::SESSION_KEY);
            if ($keyFromSession != null) {
                $this->currentItemKey = $keyFromSession;
                return $this->currentItemKey;
            }
            $this->currentItemKey = $this->groupManagement->getAllCustomersGroup()->getId();
        }
        return $this->currentItemKey;
    }

    /**
     * Retrieve customer group Id (null if all selected)
     *
     * @return int|null
     */
    public function getCustomerGroupId()
    {
        $customerGroupId = $this->getCurrentItemKey();
        if ($customerGroupId == $this->groupManagement->getAllCustomersGroup()->getId()) {
            $customerGroupId = null;
        }

        return $customerGroupId;
    }
}
