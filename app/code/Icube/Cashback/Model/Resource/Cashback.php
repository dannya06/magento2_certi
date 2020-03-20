<?php 

namespace Icube\Cashback\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Cashback extends AbstractDb
{
	/*
	* Define main table
	*/
	protected function _construct()
	{
		$this->_init('icube_cashback','entity_id');
	}

}