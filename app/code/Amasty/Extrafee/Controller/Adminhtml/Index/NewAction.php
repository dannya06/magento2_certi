<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Controller\Adminhtml\Index;

use Magento\Backend\Model\View\Result\Forward;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends Index
{
    /**
     * @return Forward
     */
    public function execute()
    {
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->forward('edit');

        return $resultForward;
    }
}
