<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Dynamic\Decimal;

use Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter\Decimal;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmInterface;
use Magento\Catalog\Model\Layer\Filter\Price\Range;
use Magento\Catalog\Model\Layer\Filter\Price\Render;

/**
 * Class Auto
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Dynamic\Decimal
 */
class Auto implements AlgorithmInterface
{
    const MIN_RANGE_POWER = 10;

    /**
     * @var Render
     */
    private $render;

    /**
     * @var Range
     */
    private $range;

    /**
     * @var Decimal
     */
    private $resource;

    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @param Render $render
     * @param Range $range
     * @param Decimal $resource
     */
    public function __construct(
        Render $render,
        Range $range,
        Decimal $resource
    ) {
        $this->render = $render;
        $this->range = $range;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsData(array $intervals = [], $additionalRequestData = '')
    {
        $data = [];
        $range = $this->range->getPriceRange() ? $this->range->getPriceRange() : $this->getRange();

        if ($range) {
            $dbRanges = $this->resource->getCount($this->getFilter(), $range);

            $dbParentRanges = $this->resource->getParentCount($this->getFilter(), $range);
            foreach (array_keys($dbParentRanges) as $key) {
                $dbParentRanges[$key] = '0';
                if (array_key_exists($key, $dbRanges)) {
                    $dbParentRanges[$key] = $dbRanges[$key];
                }
            }
            $dbRanges = $dbParentRanges;
            $data = $this->render->renderRangeData($range, $dbRanges);
        }

        return $data;
    }

    /**
     * @return number
     */
    private function getRange()
    {
        $maxPrice = floor($this->resource->getMaxValue($this->getFilter()));
        $index = 1;
        do {
            $range = pow(10, strlen(floor($maxPrice)) - $index);
            $index++;
        } while ($range > self::MIN_RANGE_POWER);

        return $range;
    }

    /**
     * Get layer filter
     *
     * @return FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set layer filter
     *
     * @param FilterInterface $filter
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }
}
