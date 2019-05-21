<?php
namespace NS8\CSP\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Order extends AbstractHelper
{
    private $logger;
    private $configHelper;
    private $productRepository;
    private $customerRepositoryInterface;
    private $countryFactory;
    private $orderManagement;
    private $orderRepository;
    private $storeManager;
    private $searchCriteriaBuilder;
    private $restClient;
    private $request;
    private $orderSyncFactory;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \NS8\CSP\Helper\Config $configHelper,
        \NS8\CSP\Helper\Logger $logger,
        \NS8\CSP\Helper\RESTClient $restClient,
        \Magento\Framework\App\RequestInterface $request,
        \NS8\CSP\Model\OrderSyncFactory $orderSyncFactory
    ) {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->productRepository = $productRepository;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->countryFactory = $countryFactory;
        $this->orderManagement = $orderManagement;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->restClient = $restClient;
        $this->request = $request;
        $this->orderSyncFactory = $orderSyncFactory;
    }

    public function getByIncrementId($incrementId)
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('increment_id', $incrementId, 'eq')->create();
            $orderList = $this->orderRepository->getList($searchCriteria)->getItems();

            if (isset($orderList) && !empty($orderList)) {
                return $this->getById(reset($orderList)->getData()['entity_id']);
            } else {
                return null;
            }
        } catch (\Exception $e) {
            $this->logger->warn('Order.getByIncrementId', $e->getMessage(), $incrementId);
            return null;
        }
    }

    public function getById($orderId)
    {
        return $this->orderRepository->get($orderId);
    }

    public function approve($orderId, $userName = null, $note = null)
    {
        $order = $this->getById($orderId);

        if (!isset($order)) {
            return;
        }

        //  must unhold prior to setting ns8status
        try {
            if ($order->getState() == 'holded') {
                $this->unhold($order->getEntityId());
            }
        } catch (\Exception $e) {
            $this->logger->error('approve', $e->getMessage(), $orderId);
        }

        //  set the approved status locally and remotely
        $this->setNS8Status($order->getIncrementId(), 'approved', $userName);

        $formattedNote = 'Approved';

        if (isset($userName) && $userName !== "") {
            $formattedNote = 'Approved by '.$userName;
        }

        if (isset($note) && $note !== "") {
            $formattedNote = $formattedNote.': '.$note;
        }

        $order->addStatusHistoryComment($formattedNote)
              ->setIsCustomerNotified(false)
              ->setIsVisibleOnFront(false)
              ->save();
    }

    public function cancel($orderId, $userName, $note = null)
    {
    	try {

		    $order = $this->getById($orderId);

		    if (!isset($order)) {
			    return;
		    }

		    $this->orderManagement->cancel($orderId);

		    //  set the status locally and remotely
		    $this->setNS8Status($order->getIncrementId(), 'canceled', $userName);

		    $formattedNote = 'Canceled';

		    if (isset($userName) && $userName !== "") {
			    $formattedNote = 'Canceled by '.$userName;
		    }

		    if (isset($note) && $note !== "") {
			    $formattedNote = $formattedNote.': '.$note;
		    }

		    $order->addStatusHistoryComment($formattedNote)
		          ->setIsCustomerNotified(false)
		          ->setIsVisibleOnFront(false)
		          ->save();
	    } catch (\Exception $e) {
		    $this->logger->error("orderHelper.cancel", $e->getMessage());
	    }
    }

    public function hold($orderId, $userName, $note = null)
    {
    	try {

		    $order = $this->getById($orderId);

		    if (!isset($order)) {
			    return;
		    }

		    if ($order->canHold()) {

			    $this->orderManagement->hold($orderId);

			    $formattedNote = 'On Hold';

			    if (isset($userName) && $userName !== "") {
				    $formattedNote = 'On Hold by '.$userName;
			    }

			    if (isset($note) && $note !== "") {
				    $formattedNote = $formattedNote.': '.$note;
			    }

			    $order->addStatusHistoryComment($formattedNote)
			          ->setIsCustomerNotified(false)
			          ->setIsVisibleOnFront(false)
			          ->save();
		    } else {

			    $order->addStatusHistoryComment('NS8 Protect: Unable to hold order.  The current order state does not allow for placing the order on hold.')
			          ->setIsCustomerNotified(false)
			          ->setIsVisibleOnFront(false)
			          ->save();
		    }
	    } catch (\Exception $e) {
		    $this->logger->error("orderHelper.hold", $e->getMessage());
	    }
    }

    public function unhold($orderId, $note = null)
    {
    	try {
		    $this->orderManagement->unhold($orderId);

		    if (isset($note) && $note !== "") {
			    $order = $this->getById($orderId);

			    $order->addStatusHistoryComment($note)
			          ->setIsCustomerNotified(false)
			          ->setIsVisibleOnFront(false)
			          ->save();
		    }
	    } catch (\Exception $e) {
		    $this->logger->error("orderHelper.unhold", $e->getMessage());
	    }
    }

    public function upsertOrderSync($order)
    {
        $orderSync = $this->orderSyncFactory
            ->create()
            ->load($order->getId(), 'order_id');

        $order_id = $orderSync->getData('order_id');

        //  if not in table, create it
        if (!isset($order_id)) {
            $this->orderSyncFactory
                ->create()
                ->setData('order_id', $order->getId())
                ->setData('increment_id', $order->getIncrementId())
                ->save();
        }
    }

    public function setOrderSyncUploaded($orderId)
    {
        $orderSync = $this->orderSyncFactory
            ->create()
            ->load($orderId, 'order_id');

        if ($orderSync->getData('status') != 0) {
            return;
        }

        $orderSync
            ->setData('status', 1)
            ->save();
    }

    public function setOrderSyncProcessed($orderId)
    {
        $this->orderSyncFactory->create()
            ->load($orderId, 'order_id')
            ->setData('status', 2)
            ->save();
    }

    public function incrementFailures($orderId)
    {
        $orderSync = $this->orderSyncFactory->create()->load($orderId, 'order_id');
        $failures = $orderSync->getData('failures');

        $orderSync
           ->setData('failures', $failures + 1)
           ->save();
    }

    public function processQueue()
    {
        try {
            $orderSync = $this->orderSyncFactory->create();
            $collection = $orderSync->getCollection()
                ->addFieldToFilter('status', ['lt' => 2])
                ->setOrder('created_at', 'ASC')
                ->setPageSize(10);

            if ($collection->getSize() == 0) {
                return;
            }

            foreach ($collection as & $row) {
                try {
                    //  set status to failed after 10 attempts
                    if ($row['failures'] > 10) {
                        $row
                            ->setData('status', 3)
                            ->save();

                        $this->logger->error("orderHelper.processQueue", 'too many failures for order '
                            .$row['increment_id'], $row);
                    } else {
                        switch ($row['status']) {
                            case 0:     //  not uploaded
                                $order = $this->getById($row['order_id']);

                                if (!isset($order)) {
                                    $this->logger->warn('orderHelper.processQueue', 'no such order: '
                                                                                    .$row['order_id']);
                                    $this->incrementFailures($row['order_id']);
                                } else {
                                    $this->upload($order, 30);    //  call from cron can have higher timeout
                                }

                                break;
                            case 1:     //  uploaded, but not processed
                                $this->sync($row['order_id']);
                                break;
                        }
                    }
                } catch (\Exception $e) {
                    $this->incrementFailures($row['order_id']);
                    $this->logger->error("orderHelper.processQueue", $e->getMessage(), $row);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error("orderHelper.processQueue", $e->getMessage());
        }
    }

    public function upload($order, $timeOut = 5)
    {
        try {
            if (!isset($order)) {
                $this->logger->warn('orderHelper.upload', 'no order');
                return;
            }

            $orderId = $order->getId();

            //  upload the order
            $response = $this->restClient->post(
                "protect/magento/orders",
                $this->toCallStructure($order),
                null,
                $timeOut
            );

            if (!isset($response)) {
                $this->logger->error('orderHelper.upload', 'Unable to upload. No response from api', $response);
                $this->incrementFailures($orderId);
                return;
            }

            if ($response->getStatus() != 200) {
                $this->logger->error('orderHelper.upload', $response->getMessage(), $orderId);
                $this->incrementFailures($orderId);
                return;
            }

            //  set the status to uploaded
            $this->setOrderSyncUploaded($orderId);
        } catch (\Exception $e) {
            $this->incrementFailures($orderId);
            $this->logger->error("orderHelper.upload error trap", $e->getMessage(), $orderId);
        }
    }

    public function sync($orderId)
    {
        try {
            if (!isset($orderId)) {
                return;
            }

            $order = $this->getById($orderId);

            if (!isset($order)) {
                $this->logger->warn('orderHelper.sync', 'no such order: '.$orderId);
                $this->incrementFailures($orderId);
                return;
            }

            $response = $this->restClient->get(
                "protect/magento/orders/"
                    .urlencode($order->getIncrementId())
                    ."/risk?accessToken=".$this->configHelper->getAccessToken(),
                null,
                null,
                60
            );

            if (!isset($response)) {
                $this->logger->warn('orderHelper.sync', 'api error: no response', $orderId);
                $this->incrementFailures($orderId);
                return;
            }

            //  if not found, it may not have finished processing - don't log error, but add to failures
            if ($response->getStatus() == 404) {
                $this->incrementFailures($orderId);
                return;
            }

            //  all other errors should log
            if ($response->getStatus() != 200) {
                $this->logger->error('orderHelper.sync', 'api error '.$response->getStatus()
                    .': '.$response->getMessage(), $orderId);
                $this->incrementFailures($orderId);
                return;
            }

            if (isset($response->data) && isset($response->data->score) && isset($response->data->NS8Status)) {
	            $order
		            ->setData('eq8_score', $response->data->score)
		            ->setData('ns8_status', $response->data->NS8Status)
		            ->save();

	            //  update the order status if indicated and it hasn't already been updated
                if (isset($response->data->riskInfo) && isset($response->data->riskInfo->setState)) {
                    $orderSync = $this->orderSyncFactory->create()->load($orderId, 'order_id');

                    //  skip if order already canceled or the same state
                    if ($order->getState() != 'canceled' && $order->getState() != $response->data->riskInfo->setState) {
                        // make sure status not already set in past

                        if ($orderSync->getData('order_status_set') == 0) {
                            switch ($response->data->riskInfo->setState) {
                                case 'canceled':
                                    $this->cancel($orderId, null, $response->data->riskInfo->message);
                                    break;
                                case 'holded':
                                    $this->hold($orderId, null, $response->data->riskInfo->message);
                                    break;
                                default:
                                    $order->setState($response->data->riskInfo->setState)
                                          ->setStatus($response->data->riskInfo->setStatus);

                                    $order->addStatusHistoryComment($response->data->riskInfo->message)
                                          ->setIsCustomerNotified(false)
                                          ->setIsVisibleOnFront(false);

                                    $order->save();
                            }
                        }
                    }

                    $orderSync->setData('order_status_set', 1)->save();
                }

                $this->setOrderSyncProcessed($orderId);
            } else {
                $this->logger->error("orderHelper.sync", 'score or status not returned from api', $orderId);
                $this->incrementFailures($orderId);
            }
        } catch (\Exception $e) {
            $this->logger->error("orderHelper.sync", $e->getMessage(), $orderId);
            $this->incrementFailures($orderId);
        }
    }

    public function setNS8Status($incrementId, $NS8Status, $userName = null)
    {
        $order = $this->getByIncrementId($incrementId);
        $order
            ->setData('ns8_status', $NS8Status)
            ->save();

        //  attempt to set remote status
        $params = [
            "accessToken" => $this->configHelper->getAccessToken(),
            "increment_id" => $incrementId,
            "NS8Status" => $NS8Status,
            "userName" => $userName
        ];

        $response = $this->restClient->post("protect/magento/orders/status", $params);

        if (!isset($response) || $response->getStatus() != 200) {
            $message = 'unknown error';

            if (isset($response)) {
                $message = $response->getMessage();
            }

            $this->logger->warn('orderHelper.setNS8Status', 'api error: '.$message, $incrementId);

            //  set resync status on order due to api error
            $this->orderSyncFactory
                ->create()
                ->load($incrementId, 'increment_id')
                ->setData('status', 0)
                ->setData('failures', 0)
                ->save();
        }
    }

    public function update($order)
    {
        try {
            //  create/update status row
            $this->upsertOrderSync($order);

            //  upload the order
            $this->upload($order);
        } catch (\Exception $e) {
            $this->logger->error("orderHelper.update", $e->getMessage());
        }
    }

    public function toCallStructure($order)
    {
        $billingAddressObj = $order->getBillingAddress();
        $shippingAddressObj = $order->getShippingAddress();
        $itemsObj = $order->getItems();
        $paymentObj = $order->getPayment();
        $billingAddress = null;
        $shippingAddress = null;
        $customer = null;
        $payment = null;
        $items = [];
        $customerObj = null;

        $customerId = $order->getCustomerId();

        if (isset($customerId)) {
            $customerObj = $this->customerRepositoryInterface->getById($customerId);
        }

        if (isset($customerObj)) {
            $customer = [
                "created_at" => $customerObj->getCreatedAt(),
                "updated_at" => $customerObj->getUpdatedAt(),
                "dob" => $customerObj->getDob(),
                "email" => $customerObj->getEmail(),
                "firstname" => $customerObj->getFirstname(),
                "lastname" => $customerObj->getLastname(),
                "prefix" => $customerObj->getPrefix(),
                "gender" => $customerObj->getGender()
            ];
        }

        if (isset($billingAddressObj)) {
            $billingAddress = $billingAddressObj->getData();

            if (isset($billingAddress["country_id"])) {
                $country = $this->countryFactory->create()->loadByCode($billingAddress["country_id"]);
                $billingAddress["country"] = $country->getName();
            }
        }

        if (isset($shippingAddressObj)) {
            $shippingAddress = $shippingAddressObj->getData();

            if (isset($shippingAddress["country_id"])) {
                $country = $this->countryFactory->create()->loadByCode($shippingAddress["country_id"]);
                $shippingAddress["country"] = $country->getName();
            }
        }

        if (isset($paymentObj)) {
            $payment = $paymentObj->getData();
        }

        if (isset($itemsObj)) {
            foreach ($itemsObj as $item) {
                $itemData = $item->getData();

                if (isset($itemData)) {
                    $product = $this->productRepository->getById($itemData['product_id']);

                    if (isset($product)) {
                        $productData = $product->getData();
                        $itemData["name"] = $productData["name"];

                        //  getting malformed object when product description has certain characters
                        $itemData["description"] = 'none';
                    }
                    array_push($items, $itemData);
                }
            }
        }

        $userId = $this->configHelper->getCookie('__na_u_'.$this->configHelper->getProjectId());
        $ua = $this->request->getServer('HTTP_USER_AGENT');
        $language = $this->request->getServer('HTTP_ACCEPT_LANGUAGE');

        return [
            "baseUrl" => $this->configHelper->getStoreBaseUrl($order['store_id']),
            "userId" => $userId,
            "accessToken" => $this->configHelper->getAccessToken(),
            "ip" => $this->configHelper->remoteAddress(),
            "ua" => $ua,
            "language" => $language,
            "order" => $order->getData(),
            "customer" => $customer,
            "billingAddress" => $billingAddress,
            "shippingAddress" => $shippingAddress,
            "payment" => $payment,
            "items" => $items,
            "shop" => $this->configHelper->getStore(),
            "phpVersion" => PHP_VERSION,
            "phpOS" => PHP_OS
        ];
    }
}
