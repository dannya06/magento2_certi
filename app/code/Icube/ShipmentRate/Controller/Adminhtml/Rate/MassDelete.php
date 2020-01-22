<?php

/**
 * Controller to catch action of delete rates
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Controller\Adminhtml\Rate;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Icube\ShipmentRate\Model\ResourceModel\RateData\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $_filter;

    protected $_collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $recordDeleted = 0;
        foreach ($collection->getItems() as $record) {
            $record->delete();
            $recordDeleted++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('icube_shipmentrate/rate/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Icube_ShipmentRate::rate_delete');
    }
}
