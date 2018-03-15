<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class StorefrontValueResolver
 * @package Aheadworks\Layerednav\Model
 */
class StorefrontValueResolver
{
    /**
     * Retrieve storefront value
     *
     * @param StoreValueInterface[] $objects
     * @param int $storeId
     * @return string
     */
    public function getStorefrontValue($objects, $storeId)
    {
        return $this->getStorefrontData($objects, $storeId, true);
    }

    /**
     * Retrieve storefront data
     *
     * @param StoreValueInterface[] $objects
     * @param int $storeId
     * @param bool $returnValue
     * @return string|null
     */
    private function getStorefrontData($objects, $storeId, $returnValue)
    {
        $storefrontValue = null;
        $minStoreIdStorefrontValue = null;
        $minStoreIdAvailable = null;
        foreach ($objects as $object) {
            if ($object->getStoreId() == $storeId) {
                $storefrontValue = $returnValue ? $object->getValue() : $object;
            }
            if (null === $minStoreIdAvailable) {
                $minStoreIdAvailable = $object->getStoreId();
            }
            if ($minStoreIdAvailable >= $object->getStoreId()
                && !empty($object->getValue())
            ) {
                $minStoreIdAvailable = $object->getStoreId();
                $minStoreIdStorefrontValue = $returnValue ? $object->getValue() : $object;
            }
        }

        return empty($storefrontValue) ? $minStoreIdStorefrontValue : $storefrontValue;
    }
}
