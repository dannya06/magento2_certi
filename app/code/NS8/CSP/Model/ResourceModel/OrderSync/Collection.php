<?php
namespace NS8\CSP\Model\ResourceModel\OrderSync;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('NS8\CSP\Model\OrderSync', 'NS8\CSP\Model\ResourceModel\OrderSync');
    }
}
