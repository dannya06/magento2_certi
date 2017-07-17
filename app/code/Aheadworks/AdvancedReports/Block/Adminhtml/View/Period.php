<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Block\Adminhtml\View;

use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;
use Aheadworks\AdvancedReports\Model\Source\Compare as CompareSource;
use Aheadworks\AdvancedReports\Model\Filter\Period as PeriodFilter;
use Aheadworks\AdvancedReports\Model\Config;
use Aheadworks\AdvancedReports\Model\Period as PeriodModel;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml\View
 */
class Period extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view/period.phtml';

    /**
     * @var PeriodSource
     */
    private $periodSource;

    /**
     * @var CompareSource
     */
    private $compareSource;

    /**
     * @var PeriodFilter
     */
    private $periodFilter;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PeriodModel
     */
    private $periodModel;

    /**
     * @param Context $context
     * @param PeriodSource $periodSource
     * @param CompareSource $compareSource
     * @param PeriodFilter $periodFilter
     * @param Config $config
     * @param PeriodModel $periodModel
     * @param [] $data
     */
    public function __construct(
        Context $context,
        PeriodSource $periodSource,
        CompareSource $compareSource,
        PeriodFilter $periodFilter,
        Config $config,
        PeriodModel $periodModel,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->periodSource = $periodSource;
        $this->compareSource = $compareSource;
        $this->periodFilter = $periodFilter;
        $this->config = $config;
        $this->periodModel = $periodModel;
    }

    /**
     * Retrieve date range types
     *
     * @return []
     */
    public function getOptions()
    {
        return array_merge(
            $this->periodSource->getOptions(),
            [['value' => PeriodFilter::PERIOD_TYPE_CUSTOM, 'label' => __('Custom date range')]]
        );
    }

    /**
     * Is compare available for current report
     *
     * @return bool
     */
    public function isCompareAvailable()
    {
        return $this->periodFilter->isCompareAvailable();
    }

    /**
     * Retrieve compare date range types
     *
     * @return []
     */
    public function getCompareOptions()
    {
        return array_merge(
            [['value' => PeriodFilter::PERIOD_TYPE_CUSTOM, 'label' => __('Custom')]],
            $this->compareSource->getOptions()
        );
    }

    /**
     * Retrieve date range periods
     *
     * @return []
     */
    public function getRanges()
    {
        $result = [];
        $rangeList = $this->periodSource->getRangeList($this->getLocaleTimezone());
        foreach ($rangeList as $key => $value) {
            /** @var \DateTime $from */
            $from = $value['from'];
            /** @var \DateTime $to */
            $to = $value['to'];
            $result[$key] = [
                'from' => $from->format('M d, Y'),
                'to' => $to->format('M d, Y'),
            ];
        }
        return $result;
    }

    /**
     * Retrieve current period
     *
     * @return []
     */
    public function getPeriod()
    {
        return $this->periodFilter->getPeriod();
    }

    /**
     * Retrieve current compare period
     *
     * @return []
     */
    public function getComparePeriod()
    {
        return $this->periodFilter->getComparePeriod();

    }

    /**
     * Retrieve first calendar date
     *
     * @return string
     */
    public function getEarliestCalendarDateAsString()
    {
        $date = new \DateTime(
            $this->periodModel->getFirstAvailableDateAsString(),
            $this->periodFilter->getLocaleTimezone()
        );
        return $date->format('Y-m-d');
    }

    /**
     * Retrieve latest calendar date
     *
     * @return string
     */
    public function getLatestCalendarDateAsString()
    {
        $date = new \DateTime('now', $this->periodFilter->getLocaleTimezone());
        return $date->format('Y-m-d');
    }

    /**
     * Retrieve locale timezone
     *
     * @return \DateTimeZone
     */
    public function getLocaleTimezone()
    {
        return $this->periodFilter->getLocaleTimezone();
    }

    /**
     * Retrieve first day of week
     *
     * @return int
     */
    public function getFirstDayOfWeek()
    {
        return $this->config->getLocaleFirstday();
    }
}
