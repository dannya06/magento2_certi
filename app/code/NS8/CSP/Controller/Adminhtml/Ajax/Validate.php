<?php
namespace NS8\CSP\Controller\Adminhtml\Ajax;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Order;
use NS8\CSP\Helper\Logger;

class Validate extends \Magento\Backend\App\Action
{
    private $configHelper;
    private $orderHelper;
    private $logger;
    private $resultJsonFactory;
    private $customerRepositoryInterface;
    private $context;
    private $restClient;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Config $configHelper,
        Order $orderHelper,
        Logger $logger,
        \NS8\CSP\Helper\RESTClient $restClient,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->logger = $logger;
        $this->orderHelper = $orderHelper;
        $this->configHelper = $configHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->context = $context;
        $this->restClient = $restClient;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            $order = null;

            if (isset($params["order_id"])) {
                $order = $this->orderHelper->getById($params["order_id"]);
            }

            if (isset($params["increment_id"])) {
                $order = $this->orderHelper->getByIncrementId($params["increment_id"]);
            }

            if (!isset($order)) {
                return $this->resultJsonFactory->create()->setData(['code' => 404, 'message' => 'No such order']);
            } else {
                $auth = $this->context->getAuth();

                $callParams = [
                    "accessToken" => $this->configHelper->getAccessToken(),
                    "email" => $order->getCustomerEmail(),
                    "orderName" => $order['increment_id'],
                    "baseUrl" => $this->configHelper->getStoreBaseUrl($order['store_id'])
                ];

	            $response = $this->restClient->post("protect/magento/orders/validate", $callParams, null, 10);

                if (!isset($response) || $response->getStatus() != 200) {
                    $this->logger->warn('orderHelper.validate', 'api error', $params);
                    return $this->resultJsonFactory->create()->setData(
                        ['code' => 500, 'message' => 'Unable to send request.']
                    );
                } else {
                    return $this->resultJsonFactory->create()->setData(
                        ['code' => 200, 'message' => 'Order validation sent.']
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('validate', $e->getMessage(), $params);
            return $this->resultJsonFactory->create()->setData(['code' => 400, 'message' => $e->getMessage()]);
        }
    }
}
