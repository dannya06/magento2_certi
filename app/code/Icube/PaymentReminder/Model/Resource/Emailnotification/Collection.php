<?php
namespace Icube\PaymentReminder\Model\Resource\Emailnotification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
	protected function _construct()
    {
        $this->_init(
            'Icube\PaymentReminder\Model\Emailnotification',
            'Icube\PaymentReminder\Model\Resource\Emailnotification'
        );
    }
}