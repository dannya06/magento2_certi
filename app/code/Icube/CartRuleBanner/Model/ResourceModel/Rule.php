<?php

namespace Icube\CartRuleBanner\Model\ResourceModel;

class Rule extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('icube_cart_rule_banner', 'rule_id');
    }
}