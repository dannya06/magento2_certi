<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Api\Data\FilterInterface;

/**
 * Class FilterListSearch
 * @package Aheadworks\Layerednav\Model\Layer
 */
class FilterListSearch extends FilterListAbstract
{
    /**
     * Get filter data objects
     *
     * @return FilterInterface[]
     */
    protected function getFilterDataObjects()
    {
        $this->sortOrderBuilder
            ->setField(FilterInterface::POSITION)
            ->setAscendingDirection();
        $this->searchCriteriaBuilder
            ->addFilter(FilterInterface::IS_FILTERABLE_IN_SEARCH, 0, 'gt')
            ->addSortOrder($this->sortOrderBuilder->create());

        return $this->filterRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }
}
