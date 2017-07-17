<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Locale\FormatInterface;
use Aheadworks\AdvancedReports\Model\Filter;
use Aheadworks\AdvancedReports\Model\Period as PeriodModel;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;

/**
 * Class DataProvider
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var []
     */
    protected $exportParams = [];

    /**
     * @var []
     */
    private $allowedRequestParams = [];

    /**
     * bool
     */
    private $compareEnabled = false;

    /**
     * @var FormatInterface
     */
    protected $localeFormat;

    /**
     * @var Filter\Store
     */
    protected $storeFilter;

    /**
     * @var Filter\Period
     */
    private $periodFilter;

    /**
     * @var PeriodModel
     */
    private $periodModel;

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
            $meta,
            $data
        );
        $this->localeFormat = $localeFormat;
        $this->storeFilter = $storeFilter;
        $this->periodFilter = $periodFilter;
        $this->periodModel = $periodModel;
        $this->applyReportFilters();
        $this->applyReportSettingFilters();
        $this->applyDefaultFilters();
        $this->prepareCompare();
    }

    /**
     * Retrieve allowed params from request
     *
     * @return []
     */
    public function getAllowedRequestParams()
    {
        return $this->allowedRequestParams;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function prepareUpdateUrl()
    {
        if (!isset($this->data['config']['filter_url_params'])) {
            return;
        }
        foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            $addToFilter = true;
            $addToGridRowUrl = false;
            $decode = false;
            if (is_array($paramValue)) {
                $addToFilter = isset($paramValue['addToFilter']) ? $paramValue['addToFilter'] : $addToFilter;
                $decode = isset($paramValue['decode']) ? $paramValue['decode'] : $decode;
                $addToGridRowUrl = isset($paramValue['useParamInGridRowUrl'])
                    ? $paramValue['useParamInGridRowUrl']
                    : $addToGridRowUrl;
                $paramValue = $paramValue['value'];
            }
            if ('*' == $paramValue) {
                $paramValue = $this->request->getParam($paramName);
            }
            if ($paramValue) {
                $this->data['config']['update_url'] = sprintf(
                    '%s%s/%s/',
                    $this->data['config']['update_url'],
                    $paramName,
                    $paramValue
                );
                if ($addToGridRowUrl) {
                    $this->allowedRequestParams[$paramName] = $paramValue;
                }
                if ($addToFilter) {
                    $this->exportParams[$paramName] = $paramValue;
                    // For product variant performance report
                    if ($paramName == 'product_id') {
                        $isProductConversionVariant = isset($this->data['config']['report_id']) ?
                            $this->data['config']['report_id'] == 'productconversion_variant' :
                            false;
                        if (!$isProductConversionVariant) {
                            $parentId = $this->request->getParam('parent_id');
                            $paramValue = ['product_id' => $paramValue, 'parent_id' => $parentId];
                        } else {
                            $paramValue = ['product_id' => $paramValue];
                        }
                    }
                    if ($decode) {
                        $paramValue = base64_decode($paramValue);
                    }
                    $this->addFilter(
                        $this->filterBuilder
                            ->setField($paramName)
                            ->setValue($paramValue)
                            ->setConditionType('eq')
                            ->create()
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];
        $arrItems['items'] = [];

        $compareSearchResult = clone $searchResult;
        $compareSearchResult->enableCompareMode();

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
            $arrItems['compareEnabled'] = $this->compareEnabled;
            if ($this->compareEnabled && count($rows) > 1) {
                $compareSearchResult->getData();
                $compareRows = $compareSearchResult->getChartRows();
                if (count($compareRows) > count($rows)) {
                    $arrItems['chart']['additional_rows'] = true;
                }
                $rows = $this->mergeChartRows($rows, $compareRows);
            }
            $arrItems['chart']['rows'] = $rows;
        }

        return $arrItems;
    }

    /**
     * Add report filters to SearchCriteria
     *
     * @return $this
     */
    protected function applyReportFilters()
    {
        return $this;
    }

    /**
     * Add filters to SearchCriteria
     *
     * @return void
     */
    private function applyDefaultFilters()
    {
        $filter = $this->filterBuilder->setField('storeFilter')->create();
        $this->addFilter($filter);
        $filter = $this->filterBuilder->setField('customerGroupFilter')->create();
        $this->addFilter($filter);
        $filter = $this->filterBuilder->setField('periodFilter')->create();
        $this->addFilter($filter);
    }

    /**
     * Add report settings filters to SearchCriteria
     *
     * @return void
     */
    private function applyReportSettingFilters()
    {
        if ($reportSettings = $this->request->getParam('report_settings')) {
            foreach ($reportSettings as $settingName => $settingValue) {
                $filter = $this->filterBuilder
                    ->setField($settingName)
                    ->setValue($settingValue)
                    ->setConditionType('eq')
                    ->create();
                $this->addFilter($filter);
            }
        }
    }

    /**
     * Check if compare is available for the report
     *
     * @return void
     */
    private function prepareCompare()
    {
        $config = $this->data['config'];
        if (isset($config['displayChart']) && $config['displayChart']) {
            if (!isset($config['chartType'])) {
                $this->periodFilter->setIsCompareAvailable(true);
                $this->compareEnabled = $this->periodFilter->isCompareEnabled();
            } else {
                $this->periodFilter->setIsCompareAvailable(false);
                $this->compareEnabled = false;
            }
        } else {
            $this->periodFilter->setIsCompareAvailable(false);
            $this->compareEnabled = false;
        }
    }

    /**
     * Merge chart rows with compare rows
     *
     * @param [] $rows
     * @param [] $compareRows
     * @return []
     */
    private function mergeChartRows($rows, $compareRows)
    {
        $result = $rows;
        if (count($rows) >= count($compareRows)) {
            foreach ($compareRows as $compareRowIndex => $compareRowValue) {
                foreach ($compareRowValue as $index => $value) {
                    $result[$compareRowIndex]['c_' . $index] = $value;
                }
            }
        } else {
            $intervalsCount = count($compareRows);
            $periodFrom = $this->periodFilter->getPeriodFrom();
            $periods = $this->periodModel->getPeriods($periodFrom, $intervalsCount);

            if ($periods['period'] == GroupbySource::TYPE_DAY) {
                foreach ($periods['intervals'] as $index => $interval) {
                    if (isset($result[$index]['date']) && $result[$index]['date'] == $interval['date']) {
                        continue;
                    }
                    $result[$index]['date'] = $interval['date'];
                }
            } else {
                foreach ($periods['intervals'] as $index => $interval) {
                    if (
                        isset($result[$index]['start_date']) &&
                        $result[$index]['start_date'] == $interval['start_date']
                    ) {
                        continue;
                    }
                    $result[$index]['start_date'] = $interval['start_date'];
                    $result[$index]['end_date'] = $interval['end_date'];
                }
            }

            foreach ($compareRows as $compareRowIndex => $compareRowValue) {
                foreach ($compareRowValue as $index => $value) {
                    $result[$compareRowIndex]['c_' . $index] = $value;
                }
            }
        }
        return $result;
    }
}
