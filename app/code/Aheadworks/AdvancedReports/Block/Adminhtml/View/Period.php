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
     * @var array
     */
    private $validRanges;

    /**
     * @param Context $context
     * @param PeriodSource $periodSource
     * @param CompareSource $compareSource
     * @param PeriodFilter $periodFilter
     * @param Config $config
     * @param PeriodModel $periodModel
     * @param array $data
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
     * @return array
     */
    public function getOptions()
    {
        $options = $this->periodSource->getOptions();
        $validRanges = $this->getValidRanges();
        foreach ($options as $key => $option) {
            if (!array_key_exists($option['value'], $validRanges)) {
                $options[$key]['disabled'] = true;
            } else {
                $options[$key]['disabled'] = false;
            }
        }
        return array_merge(
            $options,
            [
                ['value' => PeriodFilter::PERIOD_TYPE_CUSTOM, 'label' => __('Custom date range'), 'disabled' => false]
            ]
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
     * @return array
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
     * @return array
     */
    public function getRanges()
    {
        $result = [];
        $rangeList = $this->getValidRanges();
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
     * Get valid ranges
     *
     * @return array
     */
    private function getValidRanges()
    {
        if (!$this->validRanges) {
            $rangeList = $this->periodSource->getRangeList($this->getLocaleTimezone());
            $this->validRanges = [];
            foreach ($rangeList as $key => $range) {
                $validRange = $this->getValidRange($range);
                if ($validRange) {
                    $this->validRanges[$key] = $validRange;
                }
            }
        }
        return $this->validRanges;
    }

    /**
     * Get valid range
     *
     * @param array $range
     * @return array|null
     */
    private function getValidRange($range)
    {
        $earliestDate = new \DateTime(
            $this->periodModel->getFirstAvailableDateAsString(),
            $this->periodFilter->getLocaleTimezone()
        );
        $earliestDate->setTime(0, 0, 0);
        $from = clone $range['from'];
        $from->setTime(0, 0, 0);
        $to = clone $range['to'];
        $to->setTime(0, 0, 0);

        if ($to < $earliestDate) {
            return null;
        } elseif ($from < $earliestDate) {
            $range['from'] = $earliestDate;
        }

        return $range;
    }

    /**
     * Retrieve current period
     *
     * @return array
     */
    public function getPeriod()
    {
        $period = $this->periodFilter->getPeriod();
        $validRanges = $this->getValidRanges();
        if (array_key_exists($period['type'], $validRanges)) {
            $range = $validRanges[$period['type']];
            $period['from'] = $range['from'];
            $period['to'] = $range['to'];
        }
        return $period;
    }

    /**
     * Retrieve current compare period
     *
     * @return array
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
