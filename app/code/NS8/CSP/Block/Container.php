<?php
namespace NS8\CSP\Block;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Logger;

class Container extends \Magento\Framework\View\Element\Template
{
    private $context;
    public $logger;
    public $configHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Config $configHelper,
        Logger $logger,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->logger = $logger;
        $this->context = $context;
        parent::__construct($context, $data);
    }
}
