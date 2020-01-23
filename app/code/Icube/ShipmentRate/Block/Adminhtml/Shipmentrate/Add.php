<?php

/**
 * Block to show form container
 * 
 * @since 1.0.3
 * @link ./registration.php
 */

namespace Icube\ShipmentRate\Block\Adminhtml\Shipmentrate;

class Add extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'row_id';
        $this->_blockGroup = 'Icube_ShipmentRate';
        $this->_controller = 'adminhtml_shipmentrate';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->remove('reset');
    }

    public function getHeaderText()
    {
        return __('Add New Rate');
    }

    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }

        return $this->getUrl('icube_shipmentrate/rate/save');
    }
}
