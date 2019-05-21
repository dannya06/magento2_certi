<?php

namespace NS8\CSP\Block\Adminhtml\Tab;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Order;

class OrderReview extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'order-review.phtml';
    private $coreRegistry = null;
    public $configHelper;   //  needed in phtml
    public $orderHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        Config $configHelper,
        Order $orderHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->configHelper = $configHelper;
        $this->orderHelper = $orderHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    public function getTabLabel()
    {
        $order = $this->getOrder();
        $html = '';

        if (isset($order['ns8_status'])) {
            $html = '<div class="ns8-order-tab-title ns8-'.$order['ns8_status'].'-badge">'
                .' <div class="ns8-order-grid-score">'.$order['eq8_score'].'</div>'
                .' <div class="ns8-order-tab-status">'.$order['ns8_status'].'</div>'
                .'</div>';
        }
        return __('NS8 Status').$html;
    }

    public function getTabTitle()
    {
        return __('Review Order Fraud Status');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getTabClass()
    {
        return 'ajax only';
    }

    public function getClass()
    {
        return $this->getTabClass();
    }

    public function getTabUrl()
    {
        return $this->getUrl('ns8cspadmin/orderview/tab', ['_current' => true]);
    }
}
