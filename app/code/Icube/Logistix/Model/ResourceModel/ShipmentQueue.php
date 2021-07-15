<?php
  namespace Icube\Logistix\Model\ResourceModel;

class ShipmentQueue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('icube_logistix_queue', 'id');
    }

    public function getAWBByOrderId($orderId)
    {
        
        $where = $this->getConnection()->quoteInto("icube_logistix_queue.order_id = ?",$orderId);
        $sql = $this->getConnection()
                  ->select()
                  ->from('icube_logistix_queue',array('*'))
                  ->where($where);
        $data = $this->getConnection()->fetchRow($sql);
        return $data;
    }

    public function getUncomplete()
    {
        
        $sql = $this->getConnection()
                  ->select()
                  ->from('icube_logistix_queue',array('*'))
                  ->where("icube_logistix_queue.status >= 0 and status < 3");
        $data = $this->getConnection()->fetchAll($sql);
        return $data;
    }
}