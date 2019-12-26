<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */
 
namespace Icube\Brands\Block;

class Sidebar extends \Magento\Framework\View\Element\Template
{

    protected $_brandFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Icube\Brands\Model\BrandFactory $brandFactory
    ) {
        $this->_brandFactory = $brandFactory;
        parent::__construct($context);
    }
    
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getBrands()
    {
        $collection = $this->_brandFactory->create()->getCollection();
        $collection->addFieldToFilter('is_active', \Icube\Brands\Model\Status::STATUS_ENABLED);
        $collection->setOrder('name', 'ASC');
        $charBarndArray = [];
        foreach ($collection as $brand) {
            $name = trim($brand->getName());
            $charBarndArray[strtoupper($name[0])][] = $brand;
        }
        
        return $charBarndArray;
    }
}
