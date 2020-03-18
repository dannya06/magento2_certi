<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icube\PromoShipping\Model\Rule;

use Magento\SalesRule\Model\Rule;

class CalculatorFactory extends \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory
{
    
    protected $addclassByType = [
        "shipping_disc_by_amount" => \Icube\PromoShipping\Model\Rule\ByFixed::class,
        "shipping_disc_by_percent" => \Icube\PromoShipping\Model\Rule\ByPercent::class
    ];

    /**
     * @param string $type
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\DiscountInterface
     * @throws \InvalidArgumentException
     */
    public function create($type)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $newClassByType = array_merge($this->classByType, $this->addclassByType);
        try {
            if (!isset($newClassByType[$type])) {
                throw new \InvalidArgumentException($type . ' is unknown type');
            }
            return $objectManager->create($newClassByType[$type]);
        } catch (\Throwable $th) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sales-rule.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Throwable : ".$th->getMessage());
        } catch (\Throwable $th) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sales-rule.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Exception : ".$th->getMessage());
        }
        
    }
}
