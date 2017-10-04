<?php
namespace Icube\CustomStyle\Block;

class GeneralCss extends \Magento\Framework\View\Element\Template
{
    protected $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Icube\CustomStyle\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
    }

    public function getGeneralCss()
    {
        return $this->helper->getConfigValue('icube_customstyle/general/general_css', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
