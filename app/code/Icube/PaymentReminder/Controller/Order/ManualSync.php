<?php
namespace Icube\PaymentReminder\Controller\Order;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use Icube\PaymentReminder\Helper\CancelOrder;

class ManualSync extends Action
{
	protected $cancelorder;

    public function __construct(Context $context,
    	CancelOrder $cancelorder
        ){
        $this->cancelorder = $cancelorder;        
        parent::__construct($context);
    }

    public function execute()
    {
    	$this->cancelorder->migrateData();
        
    }
}