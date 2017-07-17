<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\DataProvider\CustomerSales\Customers;

/**
 * Class DataProvider
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\CustomerSales\Customers
 */
class DataProvider extends \Aheadworks\AdvancedReports\Ui\DataProvider\DataProvider
{
    /**
     * {@inheritdoc}
     */
    protected function applyReportFilters()
    {
        $filter = $this->filterBuilder->setField('rangeFilter')->create();
        $this->addFilter($filter);
        return parent::applyReportFilters();
    }
}
