<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\DataProvider\Location\Region;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Locale\FormatInterface;
use Aheadworks\AdvancedReports\Model\Filter;
use Aheadworks\AdvancedReports\Model\Period as PeriodModel;
use Aheadworks\AdvancedReports\Model\Config;

/**
 * Class DataProvider
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Location
 */
class DataProvider extends \Aheadworks\AdvancedReports\Ui\DataProvider\DataProvider
{
    /**
     * @var Config
     */
    private $config;

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
     * @param Config                $config
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
        Config $config,
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
        $this->config = $config;
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
            $countryId = $this->request->getParam('country_id');
            $countries = $this->config->getCountriesWithStateRequired();
            if (in_array($countryId, $countries)) {
                $rows = $searchResult->getChartRows();
                $arrItems['chart']['rows'] = $rows;
                if ($countryId) {
                    $arrItems['chart']['options'] = [
                        'region' => $countryId,
                        'resolution' => 'provinces'
                    ];
                }
            }
        }

        return $arrItems;
    }
}
