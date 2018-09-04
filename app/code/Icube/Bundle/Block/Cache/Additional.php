<?php
namespace Icube\Bundle\Block\Cache;

class Additional extends \Magento\Backend\Block\Template
{

    /**
     * Additional constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getJsBundleGeneratgionUrl()
    {
        return $this->getUrl('icube_bundle/cache/generateJsBundle');
    }
}
