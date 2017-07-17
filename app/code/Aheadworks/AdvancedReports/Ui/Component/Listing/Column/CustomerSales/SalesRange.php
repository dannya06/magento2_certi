<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column\CustomerSales;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;
use Aheadworks\AdvancedReports\Model\Filter\Store as StoreFilter;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;

/**
 * Class SalesRange
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column\CustomerSales
 */
class SalesRange extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @var StoreFilter
     */
    private $storeFilter;

    /**
     * @var LocaleFormat
     */
    private $localeFormat;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlModel $urlModel
     * @param StoreFilter $storeFilter
     * @param LocaleFormat $localeFormat
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlModel $urlModel,
        StoreFilter $storeFilter,
        LocaleFormat $localeFormat,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlModel = $urlModel;
        $this->storeFilter = $storeFilter;
        $this->localeFormat = $localeFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $excludeRefunded = false;
        if (isset($dataSource['data']['excludeRefunded']) && $dataSource['data']['excludeRefunded']) {
            $excludeRefunded = true;
        }

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $format = $this->localeFormat->getPriceFormat(null, $this->storeFilter->getCurrencyCode());
                $rangeFrom = number_format(
                    $item['range_from'],
                    $format['precision'],
                    $format['decimalSymbol'],
                    $format['groupSymbol']
                );
                $rangeTo = number_format(
                    $item['range_to'],
                    $format['precision'],
                    $format['decimalSymbol'],
                    $format['groupSymbol']
                );
                $fromPattern = str_replace('%s', '%1', $format['pattern']);
                $toPattern = str_replace('%s', '%2', $format['pattern']);
                if ($item['range_to']) {
                    $item['row_label'] = __($fromPattern . ' - ' . $toPattern, [$rangeFrom, $rangeTo]);
                } else {
                    $item['row_label'] = __($fromPattern . '+', [$rangeFrom]);
                }

                $params = [
                    'range_from' => $item['range_from'],
                    'range_to' => $item['range_to'],
                ];
                if ($excludeRefunded) {
                    $params['exclude_refunded'] = $excludeRefunded;
                }
                $item['row_url'] = $this->urlModel->getUrl(
                    'customersales',
                    'customersales_customers',
                    $params
                );
            }

            foreach ($dataSource['data']['totals'] as &$total) {
                $rangeFrom = number_format(
                    0,
                    $format['precision'],
                    $format['decimalSymbol'],
                    $format['groupSymbol']
                );
                $total['row_label'] = __('All Sales (%1)', __($fromPattern . '+', [$rangeFrom]));
                $params = [
                    'range_from' => 0,
                ];
                if ($excludeRefunded) {
                    $params['exclude_refunded'] = $excludeRefunded;
                }
                $total['row_url'] = $this->urlModel->getUrl(
                    'customersales',
                    'customersales_customers',
                    $params
                );
            }
        }
        return $dataSource;
    }
}
