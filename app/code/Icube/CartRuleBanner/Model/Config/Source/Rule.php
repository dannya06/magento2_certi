<?php

namespace Icube\CartRuleBanner\Model\Config\Source;

use Icube\CartRuleBanner\Model\ResourceModel\Rule\CollectionFactory;

class Rule extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $_ruleCollectionFactory;

    public function __construct(CollectionFactory $ruleCollectionFactory)
    {
        $this->_ruleCollectionFactory = $ruleCollectionFactory;
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_ruleCollectionFactory->create()->load()->toOptionArray();
            array_unshift($this->_options, ['value' => '', 'label' => __('No cart rule banner')]);
        }
        return $this->_options;
    }
}
