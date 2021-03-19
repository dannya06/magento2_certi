<?php

namespace Icube\BankList\Block\Adminhtml\Config;

use Icube\BankList\Block\Adminhtml\Config\HtmlYesNoFactory;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Backend\Block\Template\Context;

class BankList extends AbstractFieldArray
{
    /**
     * @param HtmlYesNoFactory
     */
    protected $htmlYesNoFactory;

    /**
     * @param Context $context
     * @param HtmlYesNoFactory $htmlYesNoFactory
     */
    public function __construct(
        Context $context,
        HtmlYesNoFactory $htmlYesNoFactory
    ) {
        $this->htmlYesNoFactory = $htmlYesNoFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->addColumn('bankname', [
            'label' => __('Bank Name'),
            'style' => 'width:120px;margin:0 -5px',
            'class' => 'required-entry'
        ]);
        $this->addColumn('banknumber', [
            'label' => __('Account Number'),
            'style' => 'width:120px;margin:0 -5px',
            'class' => 'required-entry'
        ]);
        $this->addColumn('placeholder', [
            'label' => __('Placeholder'),
            'style' => 'width:120px;margin:0 -5px',
            'class' => 'required-entry'
        ]);
        $this->addColumn('enable', [
            'label' => __('Enable'),
            'style' => 'width:80px;margin:0 -5px',
            'class' => 'validate-select',
            'renderer' => $this->htmlYesNoFactory->create()
        ]);
        $this->_addAfter = false;
        parent::_construct();
    }

    
}
