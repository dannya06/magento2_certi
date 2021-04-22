<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Controller\Adminhtml\Index;

use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Amasty\Extrafee\Controller\Adminhtml\Index
{
    /**
     * @return Page|Forward
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('grid');

            return $resultForward;
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu('Amasty_Extrafee::fee_manage');
        $resultPage->getConfig()->getTitle()->prepend(__('Extra Fees'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Extra Fees'), __('Extra Fees'));
        $resultPage->addBreadcrumb(__('Manage Extra Fees'), __('Manage Extra Fees'));

        return $resultPage;
    }
}
