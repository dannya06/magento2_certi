<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Model\ResourceModel\EarnRule;

use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\Hydrator as EntityManagerHydrator;
use Magento\Framework\EntityManager\TypeResolver;
use Magento\Framework\EntityManager\MapperPool;

/**
 * Class Hydrator
 * @package Aheadworks\RewardPoints\Model\ResourceModel\EarnRule
 * @codeCoverageIgnore
 */
class Hydrator extends EntityManagerHydrator
{
    /**
     * @var HydratorInterface[]
     */
    private $additionalHydrators;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param TypeResolver $typeResolver
     * @param MapperPool $mapperPool
     * @param array $additionalHydrators
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        TypeResolver $typeResolver,
        MapperPool $mapperPool,
        array $additionalHydrators = []
    ) {
        parent::__construct($dataObjectProcessor, $dataObjectHelper, $typeResolver, $mapperPool);
        $this->additionalHydrators = $additionalHydrators;
    }

    /**
     * {@inheritdoc}
     * phpcs:disable Magento2.Performance.ForeachArrayMerge
     */
    public function extract($entity)
    {
        $data = parent::extract($entity);
        /** @var HydratorInterface $hydrator */
        foreach ($this->additionalHydrators as $hydrator) {
            $data = array_merge($data, $hydrator->extract($entity));
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($entity, array $data)
    {
        $entity = parent::hydrate($entity, $data);
        /** @var HydratorInterface $hydrator */
        foreach ($this->additionalHydrators as $hydrator) {
            $entity = $hydrator->hydrate($entity, $data);
        }

        return $entity;
    }
}
