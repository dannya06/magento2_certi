<?php
namespace NS8\CSP\Controller\Adminhtml\OrderView;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class Tab extends \Magento\Sales\Controller\Adminhtml\Order
{
    private $layoutFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );
    }

    /**
     * Generate order history for ajax request
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $this->_initOrder();
        $layout = $this->layoutFactory->create();
        $html = $layout->createBlock('NS8\CSP\Block\Adminhtml\Tab\OrderReview')->toHtml();
        $this->_translateInline->processResponseBody($html);

        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($html);
        return $resultRaw;
    }
}
