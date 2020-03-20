<?php 

namespace Icube\Cashback\Model\Resource\Cashback;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

	protected $_idFieldName = 'entity_id';

	protected function _construct()
	{
		$this->_init(
			'Icube\Cashback\Model\Cashback',
			'Icube\Cashback\Model\Resource\Cashback'
		);
	}

}