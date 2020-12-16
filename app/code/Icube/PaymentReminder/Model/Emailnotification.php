<?php

namespace Icube\PaymentReminder\Model;

use Magento\Framework\Model\AbstractModel;
	 
class Emailnotification extends AbstractModel
{
	protected function _construct()
	{
	    $this->_init('Icube\PaymentReminder\Model\Resource\Emailnotification');
    }
}