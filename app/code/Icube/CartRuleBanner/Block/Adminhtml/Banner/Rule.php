<?php

namespace Icube\CartRuleBanner\Block\Adminhtml\Banner;

class Rule extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'banner_rule';
        $this->_headerText = __('Cart Rule Banner');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}