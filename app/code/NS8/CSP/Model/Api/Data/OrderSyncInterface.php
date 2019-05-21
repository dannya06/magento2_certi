<?php
namespace NS8\CSP\Model\Api\Data;

interface OrderSyncInterface
{
    public function getId();
    public function setId($id);

    public function getIncrementId();
    public function setIncrementId($increment_id);

    public function getOrderId();
    public function setOrderId($order_id);
}
