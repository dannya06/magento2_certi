<?php
namespace Icube\PaymentReminder\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Emailnotification extends AbstractDb
{
	protected function _construct()
    {
        $this->_init('icube_email_notification','entity_id');
    }
}
