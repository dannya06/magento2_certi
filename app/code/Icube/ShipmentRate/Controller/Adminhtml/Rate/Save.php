<?php

/**
 * Controller to catch action of save new / edit rate
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Controller\Adminhtml\Rate;

class Save extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Icube\ShipmentRate\Model\RateDataFactory $rateDataFactory
    ) {
        parent::__construct($context);
        $this->rateDataFactory = $rateDataFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('icube_shipmentrate/rate/add');
            return;
        }

        try {
            $rowData = $this->rateDataFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setEntityId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Rate has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('icube_shipmentrate/rate/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Icube_ShipmentRate::rate_save');
    }
}
