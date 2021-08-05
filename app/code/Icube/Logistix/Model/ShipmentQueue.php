<?php

namespace Icube\Logistix\Model;

use Magento\Framework\Model\AbstractModel;

class ShipmentQueue extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct() {
        $this->_init('Icube\Logistix\Model\ResourceModel\ShipmentQueue');
    }

    public function getAWBByOrderId($orderId)
    {
          $data = $this->getResource()->getAWBByOrderId($orderId);
          $result = '';
          if($data){
  	        if($data['no_resi'] != 'error' && $data['no_resi'] != 'processing'){
              $result = $data['no_resi'];
            }
          }
          return $result;
    }
}