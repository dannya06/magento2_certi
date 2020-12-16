<?php

namespace Icube\WidgetQty\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{		

	public function __construct(
		\Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $currentQty,
		Context $context
	) {
		$this->currentQty = $currentQty;
		parent::__construct($context);
	}

    public function getCurrentQty($sku)
    {
    	$qty = $this->currentQty->execute($sku);
        return $qty;
    }
} 