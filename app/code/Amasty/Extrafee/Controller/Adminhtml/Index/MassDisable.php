<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Controller\Adminhtml\Index;

use Amasty\Extrafee\Model\ResourceModel\Fee\CollectionFactory as FeeCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDisable extends Index
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var FeeCollectionFactory
     */
    private $feeCollectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        FeeCollectionFactory $feeCollectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->feeCollectionFactory = $feeCollectionFactory;
    }

    /**
     * @return Forward|Page
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->feeCollectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $fee) {
            $fee->setData('enabled', 0)
                ->save();
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been changed.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
