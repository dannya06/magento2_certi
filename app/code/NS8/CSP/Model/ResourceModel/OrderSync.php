<?php
namespace NS8\CSP\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class OrderSync extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('ns8_sales_order_sync', 'id');
    }
}
