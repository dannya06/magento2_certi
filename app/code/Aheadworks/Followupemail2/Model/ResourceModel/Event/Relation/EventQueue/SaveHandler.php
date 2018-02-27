<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Model\ResourceModel\Event\Relation\EventQueue;

use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\EventQueueManagementInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Followupemail2\Model\ResourceModel\Event\Relation\EventQueue
 * @codeCoverageIgnore
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var EventQueueManagementInterface
     */
    private $eventQueueManagement;

    /**
     * @param EventQueueManagementInterface $eventQueueManagement
     */
    public function __construct(
        EventQueueManagementInterface $eventQueueManagement
    ) {
        $this->eventQueueManagement = $eventQueueManagement;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var EventInterface $entity */
        $entityId = (int)$entity->getId();
        if ($entity->getStatus() == EventInterface::STATUS_DISABLED) {
            $this->eventQueueManagement->cancelEventsByEventId($entityId);
        }

        return $entity;
    }
}
