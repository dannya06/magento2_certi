<?php
namespace NS8\CSP\Controller\Adminhtml\Container;

use NS8\CSP\Helper\Config;

class Orders extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    public $configHelper;   //  needed in phtml
    private $context;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Config $configHelper
    ) {
        $this->context = $context;
        $this->configHelper = $configHelper;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('NS8_CSP::admin');
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        return $resultPage = $this->resultPageFactory->create();
    }
}
