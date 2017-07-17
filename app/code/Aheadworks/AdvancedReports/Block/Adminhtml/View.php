<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\AuthorizationInterface;
use Aheadworks\AdvancedReports\Model\Flag;
use Aheadworks\AdvancedReports\Model\Indexer\Statistics\Processor as StatisticsProcessor;

/**
 * Class View
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml
 */
class View extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view.phtml';

    /**
     * @var StatisticsProcessor
     */
    private $statisticsProcessor;

    /**
     * @var Flag
     */
    private $flag;

    /**
     * @param Context $context
     * @param Flag $flag
     * @param StatisticsProcessor $statisticsProcessor
     * @param [] $data
     */
    public function __construct(
        Context $context,
        Flag $flag,
        StatisticsProcessor $statisticsProcessor,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->flag = $flag;
        $this->statisticsProcessor = $statisticsProcessor;
    }

    /**
     * Show the date of last update reports index
     *
     * @return $this
     */
    public function showLastIndexUpdate()
    {
        $updatedAt = 'undefined';
        $flag = $this->flag->setReportFlagCode(Flag::AW_AREP_STATISTICS_FLAG_CODE)->loadSelf();
        if ($flag->hasData()) {
            $updatedAt =  $this->_localeDate->formatDate(
                $flag->getLastUpdate(),
                \IntlDateFormatter::MEDIUM,
                true
            );
        }

        return __('The latest Advanced Reports index was updated on %1.', $updatedAt);
    }

    /**
     * Can show index update text
     *
     * @return bool
     */
    public function canShowScheduleMessage()
    {
        if (
            $this->_authorization->isAllowed('Aheadworks_AdvancedReports::reports_statistics')
            && !$this->statisticsProcessor->isReindexScheduled()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Index update url
     *
     * @return string
     */
    public function getIndexUpdateUrl()
    {
        return $this->getUrl('advancedreports/statistics/schedule');
    }

    /**
     * Retrieve breadcrumbs block
     *
     * @return \Aheadworks\AdvancedReports\Block\Adminhtml\View\Breadcrumbs
     */
    public function getBreadcrumbs()
    {
        return $this->getChildBlock('breadcrumbs');
    }
}
