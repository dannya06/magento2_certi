<?php

namespace Icube\CartRuleBanner\Controller\Adminhtml\Banner\Rule;

class Index extends \Icube\CartRuleBanner\Controller\Adminhtml\Banner\Rule
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Cart Rule Banner'), __('Cart Rule Banner'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Cart Rule Banner'));
        $this->_view->renderLayout('root');
    }
}