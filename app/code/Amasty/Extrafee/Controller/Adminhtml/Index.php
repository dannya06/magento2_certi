<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;

abstract class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_Extrafee::manage';

    /**
     * @param Page $resultPage
     */
    protected function prepareDefaultTitle(Page $resultPage)
    {
        $resultPage->getConfig()->getTitle()->prepend(__('Fees'));
    }
}
