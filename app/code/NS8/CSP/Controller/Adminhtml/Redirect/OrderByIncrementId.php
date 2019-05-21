<?php
namespace NS8\CSP\Controller\Adminhtml\Redirect;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Order;

class OrderByIncrementId extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    private $configHelper;   //  needed in phtml
    private $orderHelper;
    private $resultRedirect;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Order $orderHelper,
        Config $configHelper
    ) {
        $this->configHelper = $configHelper;
        $this->orderHelper = $orderHelper;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $incrementId = $this->getRequest()->getParam('increment_id');
        $order = $this->orderHelper->getByIncrementId($incrementId);
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath(
            'sales/order/view',
            [ "order_id" => $order['entity_id'],
              "active_tab" => "ns8_order_review"
            ]
        );
        return $resultRedirect;
    }
}
