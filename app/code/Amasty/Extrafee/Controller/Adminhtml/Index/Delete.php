<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Controller\Adminhtml\Index;

use Amasty\Extrafee\Model\FeeRepository;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Index
{
    /**
     * @var FeeRepository
     */
    private $feeRepository;

    public function __construct(
        Context $context,
        FeeRepository $feeRepository
    ) {
        parent::__construct($context);
        $this->feeRepository = $feeRepository;
    }

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $feeId = (int)$this->getRequest()->getParam('id');
            $fee = $this->feeRepository->getById($feeId);
            $this->feeRepository->delete($fee);
            $this->messageManager->addSuccessMessage(__('The fee has been deleted.'));

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addErrorMessage($e->getMessage());

            // go back to edit form
            return $resultRedirect->setPath('*/*/edit', ['id' => $fee->getId()]);
        }
    }
}
