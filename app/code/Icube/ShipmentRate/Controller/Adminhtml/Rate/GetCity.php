<?php

/**
 * Ajax Controller to retrieve city by selected region
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Controller\Adminhtml\Rate;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\App\Action\Action;

class GetCity extends Action
{
    public function __construct(
        Context $context,
        \Magento\Framework\App\ResourceConnection $conn,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        $this->conn = $conn;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $region = $this->getRequest()->getParam('region');

        $options = [];
        if ($region != "") {
            $connection = $this->conn->getConnection();
            $citySql = 'SELECT city, kecamatan FROM city WHERE region_id = '.$region.' ORDER BY city';
            $cityResult = $connection->fetchAll($citySql);

            foreach ($cityResult as $city) {
                $value = $city['city'].', '.$city['kecamatan'];
                $options[$value] = $value;
            }
        }

        $result = $this->jsonFactory->create();
        return $result->setData(json_encode($options));
    }
}
