<?php

/**
 * Controller to see all advance rates
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Controller\Adminhtml\Rate;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory = false;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend((__('Shipment Rate Data')));

		return $resultPage;
	}

	protected function _getAclResource()
    {
    	$aclResource = 'Icube_ShipmentRate::shipmentrate';
    	return $aclResource;
    }
}
