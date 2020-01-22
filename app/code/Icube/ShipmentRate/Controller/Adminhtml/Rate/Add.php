<?php

/**
 * Controller to show form of new / edit rate.
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Controller\Adminhtml\Rate;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Backend\App\Action
{
    private $coreRegistry;

    private $rateDataFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Icube\ShipmentRate\Model\RateDataFactory $rateDataFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->rateDataFactory = $rateDataFactory;
    }

    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->rateDataFactory->create();
        
        if ($rowId) {
           $rowData = $rowData->load($rowId);
           if (!$rowData->getId()) {
               $this->messageManager->addError(__('Rate no longer exist.'));
               $this->_redirect('icube_shipmentrate/rate/index');
               return;
           }
        }

        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Rate') : __('Add Rate');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Icube_ShipmentRate::add_rate');
    }
}
