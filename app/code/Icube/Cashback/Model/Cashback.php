<?php 
namespace Icube\Cashback\Model;

use Magento\Framework\Model\AbstractModel;

class Cashback extends AbstractModel
{

	protected function _construct()
	{
		$this->_init('Icube\Cashback\Model\Resource\Cashback');
	}

}