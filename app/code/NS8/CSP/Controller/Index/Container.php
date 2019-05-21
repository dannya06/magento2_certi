<?php
namespace NS8\CSP\Controller\Index;

use NS8\CSP\Helper\Config;

class Container extends \Magento\Framework\App\Action\Action
{
    private $resultPageFactory;
    public $configHelper;   //  needed in phtml

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Config $configHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
