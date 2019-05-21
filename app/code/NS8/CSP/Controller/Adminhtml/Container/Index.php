<?php
namespace NS8\CSP\Controller\Adminhtml\Container;

use NS8\CSP\Helper\Config;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    private $context;
    public $configHelper;   //  needed in phtml

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

        if (isset($params["setApi"])) {
            $this->configHelper->setApiBaseUrl($params["setApi"]);
            $this->messageManager->addSuccess('API base url set to: '.$params["setApi"]);
        }

        if (isset($params["setWebsite"])) {
            $this->configHelper->setWebsiteBaseUrl($params["setWebsite"]);
            $this->messageManager->addSuccess('Website base url set to: '.$params["setWebsite"]);
        }

        if (isset($params["resetEndpoints"])) {
            $this->configHelper->resetEndpoints();
            $this->messageManager->addSuccess('API and website endpoints reset');
        }

        return $resultPage = $this->resultPageFactory->create();
    }
}
