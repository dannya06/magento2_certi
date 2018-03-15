<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Attribute;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterManagementInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Attribute
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var FilterManagementInterface
     */
    private $filterManagement;

    /**
     * @param FilterManagementInterface $filterManagement
     */
    public function __construct(
        FilterManagementInterface $filterManagement
    ) {
        $this->filterManagement = $filterManagement;
    }

    /**
     * @param FilterInterface $entity
     * @param array $arguments
     * @return FilterInterface
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if (in_array($entity->getType(), FilterInterface::ATTRIBUTE_FILTER_TYPES)) {
            if (!$this->filterManagement->synchronizeAttribute($entity->getId(), false)) {
                throw new \Exception(__('Can not synchronize linked attribute!'));
            }
        }

        return $entity;
    }
}
