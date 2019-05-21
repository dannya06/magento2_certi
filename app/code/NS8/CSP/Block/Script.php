<?php
namespace NS8\CSP\Block;

use NS8\CSP\Helper\Config;

class Script extends \Magento\Framework\View\Element\Template
{
    private $configHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    public function projectId()
    {
        return $this->configHelper->getProjectId();
    }

    public function storeId()
    {
        return $this->configHelper->getStoreId();
    }
}
