<?php
namespace NS8\CSP\Model;

use Magento\Framework\Model\AbstractModel;

class OrderSync extends AbstractModel implements \NS8\CSP\Model\Api\Data\OrderSyncInterface
{
    protected function _construct()
    {
        $this->_init('NS8\CSP\Model\ResourceModel\OrderSync');
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    public function setOrderId($id)
    {
        return $this->setData('order_id', $id);
    }

    public function getIncrementId()
    {
        return $this->getData('increment_id');
    }

    public function setIncrementId($increment_id)
    {
        return $this->setData('increment_id', $increment_id);
    }
}
