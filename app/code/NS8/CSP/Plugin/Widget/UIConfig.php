<?php
namespace NS8\CSP\Plugin\Widget;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Order;
use NS8\CSP\Helper\Logger;

class UIConfig
{
    private $context = null;
    private $configHelper;
    private $orderHelper;
    private $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Config $configHelper,
        Order $orderHelper,
        Logger $logger
    ) {
        $this->context = $context;
        $this->configHelper = $configHelper;
        $this->orderHelper = $orderHelper;
        $this->logger = $logger;
    }

    public function beforePushButtons(
        \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor $subject,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        $request = $context->getRequest();

        if ($request->getFullActionName() == 'sales_order_view') {
            $orderId = $request->getParam('order_id');
            $order = $this->orderHelper->getById($orderId);

            $url = $this->configHelper->getAdminUrl("ns8cspadmin/orderview/approve", [ "order_id" => $orderId ]);

            if ($order->getState() != 'canceled' && $order['ns8_status'] != 'approved') {
                $approveAjaxUrl = $this->configHelper->getAdminUrl("ns8cspadmin/ajax/approve");

                $buttonList->add(
                    'ns8_order_approve',
                    [
                        'label' => __('Approve'),
                        'onclick' => "NS8CSPLib.approveOrder({ order_id: '".$orderId
                                     ."', successMessage: 'Order approved.' }, '"
                                     .$approveAjaxUrl."', function(err) { if (!err) location.reload()})",
                        'sort_order' => 100,
                        'class' => 'reset'
                    ]
                );
            }
        }
    }
}
