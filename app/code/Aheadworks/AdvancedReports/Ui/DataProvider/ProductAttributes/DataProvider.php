<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\DataProvider\ProductAttributes;

/**
 * Class DataProvider
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\ProductAttributes
 */
class DataProvider extends \Aheadworks\AdvancedReports\Ui\DataProvider\DataProvider
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function applyReportFilters()
    {
        $filters = [];
        if ($conditions = $this->request->getParam('conditions')) {
            foreach ($conditions as $key => $condition) {
                if ($key != 'placeholder') {
                    if (isset($condition['inputValue']) && $condition['inputValue'] != '') {
                        $value = $condition['inputValue'];
                    } elseif (isset($condition['selectValue']) && $condition['selectValue'] != '') {
                        $value = $condition['selectValue'];
                    } elseif (isset($condition['dateValue']) && $condition['dateValue'] != '') {
                        $value = $condition['dateValue'];
                    } else {
                        $value = '';
                    }
                    $filters[] = [
                        'attribute' => $condition['attribute'],
                        'condition' => [$condition['condition'] => $value],
                        'operator'  => $condition['operator']
                    ];
                }
            }

            $filter = $this->filterBuilder
                ->setField('attributeFilter')
                ->setValue($filters)
                ->setConditionType('eq')
                ->create();
            $this->addFilter($filter);
        }
    }
}
