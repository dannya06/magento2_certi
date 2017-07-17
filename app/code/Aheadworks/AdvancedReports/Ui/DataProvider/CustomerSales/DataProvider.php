<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\DataProvider\CustomerSales;

use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Class DataProvider
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\CustomerSales
 */
class DataProvider extends \Aheadworks\AdvancedReports\Ui\DataProvider\DataProvider
{
    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];
        $arrItems['items'] = [];

        $arrItems['totals'][] = $searchResult->getTotals();
        $arrItems['totalRecords'] = $searchResult->getTotalCount();
        $arrItems['priceFormat'] = $this->localeFormat->getPriceFormat(null, $this->storeFilter->getCurrencyCode());
        $arrItems['exportParams'] = $this->exportParams;

        foreach ($searchResult->getItems() as $item) {
            $itemData = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $arrItems['items'][] = $itemData;
        }

        $config = $this->data['config'];
        if (isset($config['displayChart']) && $config['displayChart']) {
            $rows = $searchResult->getChartRows();
            $arrItems['chart']['rows'] = $this->formatChartRows($rows);
        }

        $arrItems['excludeRefunded'] = $searchResult->getExcludeRefunded();

        return $arrItems;
    }

    /**
     * Format chart rows
     *
     * @param [] $rows
     * @return []
     */
    private function formatChartRows($rows)
    {
        $formattedRows = [];
        foreach ($rows as $row) {
            $row['sales_range'] = $this->formatChartRange($row['range_from'], $row['range_to']);
            $formattedRows[] = $row;
        }
        return $formattedRows;
    }

    /**
     * Format chart range
     *
     * @param int $from
     * @param int $to
     * @return \Magento\Framework\Phrase
     */
    private function formatChartRange($from, $to)
    {
        $format = $this->localeFormat->getPriceFormat(null, $this->storeFilter->getCurrencyCode());
        $rangeFrom = number_format(
            $from,
            $format['precision'],
            $format['decimalSymbol'],
            $format['groupSymbol']
        );
        $rangeTo = number_format(
            $to,
            $format['precision'],
            $format['decimalSymbol'],
            $format['groupSymbol']
        );
        $fromPattern = str_replace('%s', '%1', $format['pattern']);
        $toPattern = str_replace('%s', '%2', $format['pattern']);
        if ($to) {
            $resultRange = __($fromPattern . ' - ' . $toPattern, [$rangeFrom, $rangeTo]);
        } else {
            $resultRange = __($fromPattern . '+', [$rangeFrom]);
        }
        return $resultRange;
    }
}
