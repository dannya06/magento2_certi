<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\DataProvider\Location;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Locale\FormatInterface;
use Aheadworks\AdvancedReports\Model\Filter;
use Aheadworks\AdvancedReports\Model\Period as PeriodModel;
use Aheadworks\AdvancedReports\Model\Source\Country as CountrySource;

/**
 * Class DataProvider
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Location
 */
class DataProvider extends \Aheadworks\AdvancedReports\Ui\DataProvider\DataProvider
{
    /**
     * @var CountrySource
     */
    private $countrySource;

    /**
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param Reporting             $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface      $request
     * @param FilterBuilder         $filterBuilder
     * @param FormatInterface       $localeFormat
     * @param Filter\Store          $storeFilter
     * @param Filter\Period         $periodFilter
     * @param PeriodModel           $periodModel
     * @param CountrySource         $countrySource
     * @param array                 $meta
     * @param array                 $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        FormatInterface $localeFormat,
        Filter\Store $storeFilter,
        Filter\Period $periodFilter,
        PeriodModel $periodModel,
        CountrySource $countrySource,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $localeFormat,
            $storeFilter,
            $periodFilter,
            $periodModel,
            $meta,
            $data
        );
        $this->countrySource = $countrySource;
    }

    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];
        $arrItems['items'] = [];

        foreach ($searchResult->getItems() as $item) {
            $itemData = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $arrItems['items'][] = $itemData;
        }
        $arrItems['totalRecords'] = $searchResult->getTotalCount();
        $arrItems['totals'][] = $searchResult->getTotals();
        $arrItems['priceFormat'] = $this->localeFormat->getPriceFormat(null, $this->storeFilter->getCurrencyCode());
        $arrItems['exportParams'] = $this->exportParams;

        $config = $this->data['config'];
        if (isset($config['displayChart']) && $config['displayChart']) {
            $rows = $searchResult->getChartRows();
            foreach ($rows as &$row) {
                $row['country'] = $this->countrySource->getOptionByValue($row['country_id']);
            }
            $arrItems['chart']['rows'] = $rows;
            $arrItems['chart']['options'] = [
                'region' => 'world',
                'resolution' => 'countries'
            ];
        }

        return $arrItems;
    }
}
