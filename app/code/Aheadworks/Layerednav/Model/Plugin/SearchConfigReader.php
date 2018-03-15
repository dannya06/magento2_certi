<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Plugin;

use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Search\Request\BucketInterface;

/**
 * Class SearchConfigReader
 *
 * @package Aheadworks\Layerednav\Model\Plugin
 */
class SearchConfigReader
{
    /**
     * Remove unused aggregation buckets
     *
     * @param ReaderInterface $subject
     * @param \Closure $proceed
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRead(
        ReaderInterface $subject,
        \Closure $proceed,
        $scope = null
    ) {
        $result = $proceed($scope);
        foreach ($result as $containerKey => $container) {
            foreach ($container['aggregations'] as $bucketKey => $bucket) {
                if ($bucket['type'] == BucketInterface::TYPE_DYNAMIC) {
                    unset($result[$containerKey]['aggregations'][$bucketKey]);
                }
            }
        }
        return $result;
    }
}
