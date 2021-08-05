<?php

namespace Icube\Logistix\Cron;

class GetLogistixStatus
{
    protected $helper;

    public function __construct(
		\Icube\Logistix\Helper\Data $helper
	)
	{
		$this->helper = $helper;
	}
	
	public function execute()
	{		
        $this->helper->getLogistix();
        
        return $this;
	}
    
}