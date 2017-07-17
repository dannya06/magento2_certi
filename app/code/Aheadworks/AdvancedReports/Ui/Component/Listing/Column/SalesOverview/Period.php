<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column\SalesOverview;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Period as PeriodModel;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column\SalesOverview
 */
class Period extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var PeriodModel
     */
    private $periodModel;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PeriodModel $periodModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PeriodModel $periodModel,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->periodModel = $periodModel;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $period = $this->periodModel
                    ->getPeriod($item, $this->context->getDataProvider()->getAllowedRequestParams());
                $item['row_url'] = $period['period_url'];
                $item['row_label'] = $period['period_label'];
            }
        }
        if (isset($dataSource['data']['chart']['rows'])) {
            foreach ($dataSource['data']['chart']['rows'] as &$item) {
                $truncEndDate = true;
                if (
                    isset($dataSource['data']['compareEnabled']) &&
                    $dataSource['data']['compareEnabled'] &&
                    isset($dataSource['data']['chart']['additional_rows']) &&
                    $dataSource['data']['chart']['additional_rows']
                ) {
                    $truncEndDate = false;
                }
                if (isset($item['date'])) {
                    $from = $item['date'];
                    $to = $item['date'];
                    $item['period'] = $this->periodModel->getPeriodFromString($from, $to, $truncEndDate);
                } else if (isset($item['start_date']) && isset($item['end_date'])) {
                    $from = $item['start_date'];
                    $to = $item['end_date'];
                    $item['period'] = $this->periodModel->getPeriodFromString($from, $to, $truncEndDate);
                }

                if (isset($item['c_date'])) {
                    $from = $item['c_date'];
                    $to = $item['c_date'];
                    $item['c_period'] = $this->periodModel->getComparePeriodFromString($from, $to);
                } else if (isset($item['c_start_date']) && isset($item['c_end_date'])) {
                    $from = $item['c_start_date'];
                    $to = $item['c_end_date'];
                    $item['c_period'] = $this->periodModel->getComparePeriodFromString($from, $to);
                }
            }
        }
        return $dataSource;
    }
}
