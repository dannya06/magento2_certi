<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Controller\Adminhtml\CustomerSales\Customers;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Aheadworks\AdvancedReports\Model\Filter\Store as StoreFilter;
use Aheadworks\AdvancedReports\Model\Filter\Range as RangeFilter;

/**
 * Class Index
 *
 * @package Aheadworks\AdvancedReports\Controller\Adminhtml\CustomerSales\Customers
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReports::reports_customersales';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var LocaleFormat
     */
    private $localeFormat;

    /**
     * @var StoreFilter
     */
    private $storeFilter;

    /**
     * @var RangeFilter
     */
    private $rangeFilter;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param LocaleFormat $localeFormat
     * @param StoreFilter $storeFilter
     * @param RangeFilter $rangeFilter
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LocaleFormat $localeFormat,
        StoreFilter $storeFilter,
        RangeFilter $rangeFilter
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->localeFormat = $localeFormat;
        $this->storeFilter = $storeFilter;
        $this->rangeFilter = $rangeFilter;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rangeTitle = $this->getRangeTitle();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_AdvancedReports::reports_customersales');
        $resultPage->getConfig()->getTitle()->prepend(__('Customers (%1)', $rangeTitle));
        return $resultPage;
    }

    /**
     * Get range title
     *
     * @return \Magento\Framework\Phrase
     */
    private function getRangeTitle()
    {
        $range = $this->rangeFilter->getRange();
        $format = $this->localeFormat->getPriceFormat(null, $this->storeFilter->getCurrencyCode());
        $rangeFrom = number_format(
            $range['from'],
            $format['precision'],
            $format['decimalSymbol'],
            $format['groupSymbol']
        );
        $rangeTo = number_format(
            $range['to'],
            $format['precision'],
            $format['decimalSymbol'],
            $format['groupSymbol']
        );
        $fromPattern = str_replace('%s', '%1', $format['pattern']);
        $toPattern = str_replace('%s', '%2', $format['pattern']);
        if ($range['to']) {
            $rangeTitle = __($fromPattern . ' - ' . $toPattern, [$rangeFrom, $rangeTo]);
        } else {
            $rangeTitle = __($fromPattern . '+', [$rangeFrom]);
        }
        return $rangeTitle;
    }
}
