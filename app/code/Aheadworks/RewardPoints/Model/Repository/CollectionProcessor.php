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
namespace Aheadworks\RewardPoints\Model\Repository;

use Aheadworks\RewardPoints\Model\ResourceModel\AbstractCollection;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\ConfigurationMismatchException;

/**
 * Class CollectionProcessor
 * @package Aheadworks\RewardPoints\Model\Repository
 */
class CollectionProcessor implements CollectionProcessorInterface
{
    /**
     * @var CollectionProcessorInterface[]
     */
    private $processors;

    /**
     * @param CollectionProcessorInterface[] $processors
     */
    public function __construct(
        $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Process collection
     *
     * @param SearchCriteria $searchCriteria
     * @param AbstractCollection $collection
     * @throws \Exception
     */
    public function process($searchCriteria, $collection)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof CollectionProcessorInterface) {
                throw new ConfigurationMismatchException(
                    __('Collection processor must implement %1', CollectionProcessorInterface::class)
                );
            }
            $processor->process($searchCriteria, $collection);
        }
    }
}
