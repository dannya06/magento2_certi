<?php

namespace Icube\CartRuleBanner\Controller\Adminhtml\Banner\Rule;

class NewAction extends \Icube\CartRuleBanner\Controller\Adminhtml\Banner\Rule
{
    /**
     * New action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}