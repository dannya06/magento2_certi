<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WeltPixel\Command\Controller\Adminhtml\Cache;

use Magento\Framework\Controller\ResultFactory;

class CleanLess extends \Magento\Backend\Controller\Adminhtml\Cache
{
    /**
     * Clean static files cache
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $generateLessCommand = $this->_objectManager->get('WeltPixel\Command\Console\Command\GenerateLessCommand');
        $observer = $this->_objectManager->get('\Magento\Framework\Event\Observer');
        $generationContainer = $generateLessCommand->getGenerationContainer();
        $successMsg = [];
        $errorMsg = [];

        foreach ($generationContainer as $key => $item) {
            try {
                $item->execute($observer);
                $successMsg[] = $key . ' module less was generated successfully.';
            } catch (\Exception $ex) {
                $errorMsg[] = $key . ' module less was not generated.';
            }
        }

        if (count($errorMsg)) {
            $this->messageManager->addError(implode("<br/>", $errorMsg));
        }

        if (count($successMsg)) {
            $this->messageManager->addSuccess(implode("<br/>", $successMsg));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('adminhtml/*');
    }
}
