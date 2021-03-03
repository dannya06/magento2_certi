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
namespace Aheadworks\RewardPoints\Model\Repository\CollectionProcessor;

use Aheadworks\RewardPoints\Model\Repository\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;

/**
 * Class SortOrderProcessor
 * @package Aheadworks\RewardPoints\Model\Repository\CollectionProcessor
 */
class SortOrderProcessor implements CollectionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($searchCriteria, $collection)
    {
        /** @var SearchCriteria $searchCriteria */
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }
    }
}
