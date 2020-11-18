<?php
namespace Icube\PaymentReminder\Cron;

use Icube\PaymentReminder\Helper\CancelOrder;

class Job {
 
   protected $cancelorder;
 
    public function __construct(CancelOrder $cancelorder) {
        $this->cancelorder = $cancelorder;  
    }
 
    public function execute() {
    	
    	$this->cancelorder->checkPendingOrder();
        
        return $this;
    }

     public function sync() {
        
        $this->cancelorder->migrateData();
        return $this; 
    }
}