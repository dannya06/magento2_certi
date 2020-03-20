<?php
namespace Icube\CustomStyle\Block\Cache;

class Additionalfpc extends \Magento\Backend\Block\Template
{

    /**
     * Additional constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getClearPageUrl()
    {
        return $this->getUrl('customstyle/cache/clearpage');
    }
}
