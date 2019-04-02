<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Rma\Model\Status;

/**
 * Class RestrictionsPool
 *
 * @package Aheadworks\Rma\Model\Status
 */
class RestrictionsPool
{
    /**
     * @var RestrictionsInterfaceFactory
     */
    private $restrictionsFactory;

    /**
     * @var array
     */
    private $customerRestrictions = [];

    /**
     * @var array
     */
    private $adminRestrictions = [];

    /**
     * @var RestrictionsInterface[]
     */
    private $customerRestrictionsInstances = [];

    /**
     * @var RestrictionsInterface[]
     */
    private $adminRestrictionsInstances = [];

    /**
     * @param RestrictionsInterfaceFactory $restrictionsFactory
     * @param array $customerRestrictions
     * @param array $adminRestrictions
     */
    public function __construct(
        RestrictionsInterfaceFactory $restrictionsFactory,
        $customerRestrictions = [],
        $adminRestrictions = []
    ) {
        $this->restrictionsFactory = $restrictionsFactory;
        $this->customerRestrictions = $customerRestrictions;
        $this->adminRestrictions = $adminRestrictions;
    }

    /**
     * Retrieves restrictions instance
     *
     * @param int $status
     * @param bool $isAdmin
     * @return RestrictionsInterface
     * @throws \Exception
     */
    public function getRestrictions($status, $isAdmin)
    {
        $restrictionsInstance = $this->getRestrictionsInstanceByType($isAdmin);
        if (!isset($restrictionsInstance[$status])) {
            $restrictions = $this->getRestrictionsByType($isAdmin);
            if (!isset($restrictions[$status])) {
                throw new \Exception(sprintf('Unknown status: %s requested', $status));
            }
            $instance = $this->restrictionsFactory->create(['data' => $restrictions[$status]]);
            if (!$instance instanceof RestrictionsInterface) {
                throw new \Exception(
                    sprintf('Restrictions instance %s does not implement required interface.', $status)
                );
            }
            $restrictionsInstance = $this->cachedRestrictionsByType($status, $instance, $isAdmin);
        }
        return $restrictionsInstance[$status];
    }

    /**
     * Retrieve restrictions by type
     *
     * @param bool $isAdmin
     * @return array
     */
    private function getRestrictionsByType($isAdmin)
    {
        return $isAdmin ? $this->adminRestrictions : $this->customerRestrictions;
    }

    /**
     * Retrieve restrictions instance by type
     *
     * @param bool $isAdmin
     * @return RestrictionsInterface[]
     */
    private function getRestrictionsInstanceByType($isAdmin)
    {
        return $isAdmin ? $this->adminRestrictionsInstances : $this->customerRestrictionsInstances;
    }

    /**
     * Cached restrictions by type
     *
     * @param int $status
     * @param RestrictionsInterface $instance
     * @param bool $isAdmin
     * @return array
     */
    private function cachedRestrictionsByType($status, $instance, $isAdmin)
    {
        if ($isAdmin) {
            $this->adminRestrictionsInstances[$status] = $instance;

            return $this->adminRestrictionsInstances;
        } else {
            $this->customerRestrictionsInstances[$status] = $instance;

            return $this->customerRestrictionsInstances;
        }
    }
}
