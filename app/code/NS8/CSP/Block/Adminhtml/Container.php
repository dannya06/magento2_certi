<?php
namespace NS8\CSP\Block\Adminhtml;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Order;

class Container extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    private $context;
    public $configHelper;   //  needed in phtml
    public $orderHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Config $configHelper,
        Order $orderHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->context = $context;
        $this->configHelper = $configHelper;
        $this->orderHelper = $orderHelper;
    }
}
