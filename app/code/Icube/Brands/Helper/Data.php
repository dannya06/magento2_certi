<?php
namespace Icube\Brands\Helper;

use \Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	
	public function getConfig()
	{
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$scopeConfig = $om->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $scopeConfig->getValue(
            'icube_brands/config/attribute_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $value;
	}
}