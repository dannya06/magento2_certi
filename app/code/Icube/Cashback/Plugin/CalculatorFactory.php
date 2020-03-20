<?php

namespace Icube\Cashback\Plugin;

class CalculatorFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $cashbackDataHelper;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Icube\Cashback\Helper\Data $cashbackDataHelper
    )
    {
        $this->_objectManager = $objectManager;
        $this->cashbackDataHelper = $cashbackDataHelper;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $subject
     * @param callable                                                        $proceed
     * @param                                                                 $type
     *
     * @return mixed
     */
    public function aroundCreate(
        \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $subject,
        \Closure $proceed,
        $type
    ) {
        $cashback = $this->cashbackDataHelper->getStaticCashbackTypes();
        if(isset($cashback[$type])){
            $path = $this->cashbackDataHelper->getFilePath($type);
            return $this->_objectManager->create($path);
        }else{
            return $proceed($type);
        }       
    }
}
