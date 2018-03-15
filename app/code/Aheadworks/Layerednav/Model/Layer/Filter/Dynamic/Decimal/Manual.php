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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Manual
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Dynamic\Decimal
 */
class Manual implements AlgorithmInterface
{
    const XML_PATH_RANGE_MAX_INTERVALS = 'catalog/layered_navigation/price_range_max_intervals';

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
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @param Render $render
     * @param Range $range
     * @param Decimal $resource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Render $render,
        Range $range,
        Decimal $resource,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->render = $render;
        $this->range = $range;
        $this->resource = $resource;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsData(array $intervals = [], $additionalRequestData = '')
    {
        $data = [];
        $range = $this->range->getPriceRange() ?
            $this->range->getPriceRange() :
            $this->range->getConfigRangeStep();

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
            $dbRanges = $this->processRange($dbRanges);
            $data = $this->render->renderRangeData($range, $dbRanges);
        }

        return $data;
    }

    /**
     * @param array $items
     * @return array
     */
    private function processRange($items)
    {
        $i = 0;
        $lastIndex = null;
        $maxIntervalsNumber = (int)$this->scopeConfig->getValue(
            self::XML_PATH_RANGE_MAX_INTERVALS,
            ScopeInterface::SCOPE_STORE
        );
        foreach ($items as $k => $v) {
            ++$i;
            if ($i > 1 && $i > $maxIntervalsNumber) {
                $items[$lastIndex] += $v;
                unset($items[$k]);
            } else {
                $lastIndex = $k;
            }
        }
        return $items;
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
