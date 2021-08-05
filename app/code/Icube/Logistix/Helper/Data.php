<?php 

namespace Icube\Logistix\Helper;

use Magento\Framework\App\Helper\Context;
use Icube\Logistix\Model\Carrier\Logistix;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const XML_SANDBOX_URL_CONFIG = 'carriers/logistix/sandbox_url';
    const XML_PROD_URL_CONFIG = 'carriers/logistix/production_url';
    const XML_SANDBOX_FLAG = 'carriers/logistix/use_sandbox';
	const XML_BRAND_ID = 'carriers/logistix/brand_id';
	const XML_CLIENT_ID = 'carriers/logistix/client_id';
	const XML_CLIENT_SECRET = 'carriers/logistix/client_secret';
	const XML_TOKEN_URL = 'carriers/logistix/token_url';
    const XML_ORIGIN_COORD = 'carriers/logistix/origin_coord';
	const XML_ORIGIN_ID = 'carriers/logistix/origin_id';
	const XML_ORIGIN_ADDRESS = 'carriers/logistix/origin_address';
	const XML_CONSIGNER_NAME = 'carriers/logistix/consigner_name';
	const XML_CONSIGNER_PHONE = 'carriers/logistix/consigner_phone';
	const XML_CONSIGNER_EMAIL = 'carriers/logistix/consigner_email';
	const XML_CONSIGNER_POSTCODE = 'carriers/logistix/consigner_postcode';
    const XML_API_KEY = 'carriers/logistix/api_key';
    const XML_ALLOW_FREE = 'carriers/logistix/allow_free';
    const XML_MIN_FREE_AMOUNT = 'carriers/logistix/min_freeshipping_amount';
    const XML_FREE_METHOD = 'carriers/logistix/specific_service_free';
    const XML_ENABLE_METHOD_FREE = 'carriers/logistix/specific_method_free';
    const XML_METHOD_OPTION = 'carriers/logistix/method_option';
	const XML_SPESIFIC_METHOD = 'carriers/logistix/specific_method';
    const XML_AUTO_BOOKING = 'carriers/logistix/auto_booking';
    const XML_DEBUG = 'carriers/logistix/debug';
    
    protected $_shipmentQueue;
    protected $_orderFactory;
    protected $_shipmentRepository;
    protected $_trackResource;
    protected $_trackFactory;
    protected $_modelCityFactory;
    protected $_shipmentNotifier; 
    protected $scopeConfig;   
	protected $_objectManager;
    protected $_convertOrder;
	protected $_shipmentOrder;
	protected $_product;
	private $logger;
    
    public function __construct(
        \Icube\Logistix\Model\ShipmentQueueFactory $shipmentQueue,
        \Magento\Sales\Model\Order $orderFactory,
        \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track $trackResource,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Icube\City\Model\CityFactory $modelCityFactory,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier, 
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
		\Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader,
		\Icube\Logistix\Model\ResourceModel\ShipmentQueue $shipmentOrder,
		\Magento\Catalog\Model\Product $product,
		\Psr\Log\LoggerInterface $logger
    ){
        $this->_shipmentQueue = $shipmentQueue;
        $this->_orderFactory = $orderFactory;
        $this->_shipmentRepository = $shipmentRepository;
        $this->_trackResource = $trackResource;
        $this->_trackFactory = $trackFactory;
        $this->_modelCityFactory = $modelCityFactory;
		$this->_shipmentNotifier = $shipmentNotifier;
		$this->_convertOrder = $convertOrder;
        $this->scopeconfig = $config;
        $this->_objectManager = $objectManager;
        $this->_shipmentOrder = $shipmentOrder;
		$this->_product = $product;
		$this->logger = $logger;
        $objectManager->configure($configLoader->load('frontend'));
    }

    public function getDebugMode()
    {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $value = $scopeConfig->getValue(self::XML_DEBUG, $storeScope);
        return $value;
    }

	public function getLogistix()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		
		// GET ready_to_ship ORDER
		$orderCollection = $this->_orderFactory->getCollection();
		$orderCollection->addFieldToFilter('main_table.status', array('eq' => 'ready_to_ship'));
		$orderCollection->addFieldToFilter('main_table.shipping_method', array('like' => 'logistix_%'));
		
		foreach($orderCollection as $order){
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/exception-logistix.log');
	        $logger = new \Zend\Log\Logger();
	        $logger->addWriter($writer);
			
			// CREATE SHIPMENT START
			if (!$order->canShip()) {
		        $logger->info("You can't create the Shipment of this order :".$order->getId());
			}
	
			$orderShipment = $this->_convertOrder->toShipment($order);
			$packWeight = 0;
			$itemList = [];
			foreach ($order->getAllItems() as $orderItem) {
				if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
					continue;
				}
				$itemList[] = array(
					"name" => $orderItem->getName(),
					"qty" => $orderItem->getQtyToShip(),
					"value"=>$orderItem->getRowTotal()
				);
				$packWeight += ($orderItem->getWeight() * $orderItem->getQtyToShip()) ;     
				$qty = $orderItem->getQtyToShip();
				$shipmentItem = $this->_convertOrder->itemToShipmentItem($orderItem)->setQty($qty);
				$orderShipment->addItem($shipmentItem);
			}
	
			$orderShipment->register();
			$orderShipment->getOrder()->setIsInProcess(true);
			try {
				try {
					$sourceCommand = $objectManager->create('Magento\Inventory\Model\Source\Command\GetSourcesAssignedToStockOrderedByPriority');
					$stockResolver = $objectManager->create('Magento\InventorySales\Model\StockResolver');
		            $orderShipment->getExtensionAttributes()
		                ->setSourceCode(
		                    array_reduce(
		                        $sourceCommand->execute(
		                            $stockResolver
		                                ->execute(
		                                    'website',
		                                    $order->getStore()
		                                        ->getWebsite()
		                                        ->getCode()
		                                )
		                                ->getStockId()
		                        ),
		                        function ($sourceCode, $source) {
		                            return $sourceCode ?: $source->getSourceCode();
		                        },
		                        false
		                    )
		                );
		        } catch (Exception $e) {
		            $logger->info("Sources Error: ".$e->getMessage());
		        }

				$orderShipment->save();
				$orderShipment->getOrder()->save();
				// CREATE SHIPMENT END
				
				// Save to QUEUE
				$shipmentCollection = $order->getShipmentsCollection();
				foreach ($shipmentCollection as $shipment) {
					$queue = $this->_shipmentQueue->create();
					$queue->setData([
						'order_id'=> $order->getId(),
						'shipment_id'=> $shipment->getId(),
						'shipment_date'=> $shipment->getCreatedAt(),
						'no_resi'=> NULL,
						'comment'=> NULL,
						'status' => 0
					])->save();
				}

				// Send Shipment Email
				$this->_shipmentNotifier->notify($orderShipment);
				$orderShipment->save();
			} catch (\Exception $e) {
		        $logger->info($e->getMessage());
			}
		}

		$queue = $this->_shipmentOrder->getUncomplete();
		foreach ($queue as $key => $q) {
			$order = $this->_orderFactory->load($queue[$key]["order_id"]);
			$shipment = $this->_shipmentRepository->get((int)$queue[$key]["shipment_id"]);
			$packWeight = 0;
			$itemList = [];
			$totalLength = 0;
			$totalWidth = 0;
			$totalHeight = 0;
			foreach ($order->getAllItems() as $orderItem) {
	            if ($orderItem->getProductType() == 'simple') {
	                $product = $this->_product->load($orderItem->getProductId());
					$price = $product->getPrice();
					if ($product->getData('dimension_package_length') && $product->getData('dimension_package_width') && $product->getData('dimension_package_height')) {
						$totalLength += (int)$product->getData('dimension_package_length');
						$totalWidth += (int)$product->getData('dimension_package_width');
						$totalHeight += (int)$product->getData('dimension_package_height');
					} else {
						$totalLength += 1;
						$totalWidth += 1;
						$totalHeight += 1;
					}
					
					$itemList[] = array(
						"name" => $orderItem->getName(),
						"qty" => (string)round($orderItem->getQtyOrdered()),
						"value" => (string)round($price)
					);
					$packWeight += (floatval($orderItem->getWeight()) * (string)round($orderItem->getQtyOrdered())) ; 
				}   
			}

			//Call API
			$paramCallApi = array(
				"order" => $order,
				"shipment" => $shipment,
				"dimension_package_length" => $totalLength,
				"dimension_package_height" => $totalHeight,
				"dimension_package_width" => $totalWidth,
				"packWeight" => $packWeight,
				"itemList" => $itemList,
				"queueStatus" => (int) $queue[$key]["status"],
				"queueId" => (int) $queue[$key]["id"],
				"queueLastResult" => $queue[$key]["result_id"]
			);
			
			$this->callAPi($paramCallApi);
		}
	}

	private function callAPi($paramCallApi)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        
        $prodUrl = $scopeConfig->getValue(self::XML_PROD_URL_CONFIG, $storeScope);
        $sandboxUrl = $scopeConfig->getValue(self::XML_SANDBOX_URL_CONFIG, $storeScope);
		$sandBoxFlag = $scopeConfig->getValue(self::XML_SANDBOX_FLAG, $storeScope);
		$autoBooking = $scopeConfig->getValue(self::XML_AUTO_BOOKING, $storeScope);

		if ($autoBooking == 1) {
			if ($sandBoxFlag == 1) {
				$url = $sandboxUrl;
			} else {
				$url = $prodUrl;
			}
			
			if($paramCallApi["queueStatus"] == 0){
				$paramCreateOrder = array(
					"scopeConfig" => $scopeConfig,
					"storeScope" => $storeScope,
					"paramCallApi" => $paramCallApi,
				);
				$response = $this->createOrder($url, $paramCreateOrder);
				if ($response["httpCode"] == 200) {
					if ($response["response"]) {
						$arrGenerateAwb = json_decode($response["response"], true);
						$queue = $this->_shipmentQueue->create()->load($paramCallApi["queueId"]);
						$queue->setComment($arrGenerateAwb['data']['generateAirwaybill']["customerReference"]);
						$queue->setStatus(3);
						$queue->setNoResi($arrGenerateAwb['data']['generateAirwaybill']["airwaybillNumber"]);
						$queue->setResultId('done');
						$queue->save();

						//save tracking number
						$track = $this->_trackFactory->create();
						$track
							->setShipment($paramCallApi["shipment"])
							->setParentId($paramCallApi["shipment"]->getId())
							->setOrderId($paramCallApi["shipment"]->getOrderId())
							->setNumber($arrGenerateAwb['data']['generateAirwaybill']["airwaybillNumber"])
							->setCarrierCode('Logistix')
							->setTitle('Logistix-'.strtoupper($arrGenerateAwb['data']['generateAirwaybill']["provider"]));
						$this->_trackResource->save($track);
					}
				}				
			}
		}
	}

	private function createOrder($url, $paramCreateOrder)
    {
		$order = $paramCreateOrder["paramCallApi"]["order"];

		$shipping = $order->getShippingMethod(true);
		$shippingMethod = $shipping["method"];
		$arrShippingMethod = explode("_", $shippingMethod);
		$arrShippingMethod = implode("-", $arrShippingMethod);
		$arrShippingMethod = explode("-", $arrShippingMethod);

		$shippingAddress = $order->getShippingAddress();
		$shippingAddressArray = $shippingAddress->getData();

		$brandId = $paramCreateOrder["scopeConfig"]->getValue(self::XML_BRAND_ID, $paramCreateOrder["storeScope"]);
		$clientId = $paramCreateOrder["scopeConfig"]->getValue(self::XML_CLIENT_ID, $paramCreateOrder["storeScope"]);
		$clientSecret = $paramCreateOrder["scopeConfig"]->getValue(self::XML_CLIENT_SECRET, $paramCreateOrder["storeScope"]);
		$tokenUrl = $paramCreateOrder["scopeConfig"]->getValue(self::XML_TOKEN_URL, $paramCreateOrder["storeScope"]);
		$originId = $paramCreateOrder["scopeConfig"]->getValue(self::XML_ORIGIN_ID, $paramCreateOrder["storeScope"]);
		$originAddress = $paramCreateOrder["scopeConfig"]->getValue(self::XML_ORIGIN_ADDRESS, $paramCreateOrder["storeScope"]);
		$consignerName = $paramCreateOrder["scopeConfig"]->getValue(self::XML_CONSIGNER_NAME, $paramCreateOrder["storeScope"]);
		$consignerPhone = $paramCreateOrder["scopeConfig"]->getValue(self::XML_CONSIGNER_PHONE, $paramCreateOrder["storeScope"]);
		$consignerEmail = $paramCreateOrder["scopeConfig"]->getValue(self::XML_CONSIGNER_EMAIL, $paramCreateOrder["storeScope"]);
		$consignerPostcode = $paramCreateOrder["scopeConfig"]->getValue(self::XML_CONSIGNER_POSTCODE, $paramCreateOrder["storeScope"]);
		$originCoord = $paramCreateOrder["scopeConfig"]->getValue(self::XML_ORIGIN_COORD, $paramCreateOrder["storeScope"]);
		$arrOriginCoord = explode(" ", $originCoord);

		$generateToken = array(
			"clientId" => $clientId,
			"clientSecret" => $clientSecret,
			"tokenUrl" => $tokenUrl
		);

		$district = '';
		$shippingCity = explode(', ', $shippingAddressArray['city']);
        if (isset($shippingCity[1])) {
            $district = $this->getDistrictOptions($generateToken, $url, $shippingCity[0], $shippingCity[1]);
        }

		$shippingDescription = $order->getShippingDescription();
		$useInsurance = 0;		
		if (strpos($shippingDescription, 'insurance') !== false) {
		    $useInsurance = 1;
		} 
		
		$parameters = [
            'input' => [
                'brandID' => $brandId,
                'referenceId' => (string)$order->getId(),
                'provider' => $arrShippingMethod[0],
                'service' => $arrShippingMethod[1],
                'package' => [
                    'price' => (float)$order['grand_total'],
                    'description' => 'ini adalah description pacakge',
                    'dimension' => [
                        'height' => $paramCreateOrder["paramCallApi"]["dimension_package_height"],
                        'length' => $paramCreateOrder["paramCallApi"]["dimension_package_length"],
                        'width' => $paramCreateOrder["paramCallApi"]["dimension_package_width"],
                    ],
                    'weight' => $paramCreateOrder["paramCallApi"]["packWeight"],
                    'useInsurance' => false,
                    'useCOD' => false,
                    'totalCOD' => 0,
                ],
                'pickupRequestTimeDate' => strtotime(date('Y-m-d H:i:s',strtotime('+7 hours'))),
                'sender' => [
                    'name' => $consignerName,
                    'phone' => $consignerPhone,
                    'email' => $consignerEmail,
                    'address' => $originAddress,
                    'postCode' => $consignerPostcode,
                    'coordinate' => [
                        'latitude' => $arrOriginCoord[0],
                        'longitude' => $arrOriginCoord[1],
                    ],
                    'note' => 'ini adalah note'
                ],
                'receiver' => [
                    'name' => $shippingAddressArray['firstname'],
                    'phone' => $shippingAddressArray['telephone'],
                    'email' => $shippingAddressArray['email'],
                    'address' => 
						$shippingAddressArray['street'].". ".
						$shippingAddressArray['city'].", ".
						$shippingAddressArray['region'].", ".
						$shippingAddressArray['postcode'],
                    'districtChannelCode' => 'srcl',
                    'district' => $district,
                    'postCode' => $shippingAddressArray['postcode'],
                    'coordinate' => [
                        'latitude' => $order->getDestLatitude(),
                        'longitude' => $order->getDestLongitude(),
                    ],
                    'note' => 'ini adalah note'
                ],
                'notes' => 'ini adalah notes',
                'credentialsFlag' => 'srcl',
                'source' => 'swift:{'.$brandId.'}',
            ],
        ];

		if ($this->getDebugMode()) {
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/logistix-generateawb-request-parameters.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info(json_encode($parameters, true));
        }

        $query = '
	        mutation generateAirwaybill($input: GenerateAirwaybillInput!){
				generateAirwaybill(input: $input){
					airwaybillNumber
					customerReference
					provider
				}
        }';

        $token = $this->generateToken($generateToken);
        $variables = json_encode($parameters);
        $headers = [
            'Content-Type: application/json',
            'Authorization: '.$token,
        ];
        $params = json_encode([
            'query' => $query,
            'variables' => $variables,
        ]);

        $result = $this->runApi($url, 'POST', $params, $headers);

        return $result;
	}

	public function getDistrictOptions($generateToken, $url, $city, $suburb) 
    {
        $token = $this->generateToken($generateToken);
        $headers = [
	        'Content-Type: application/json',
	        'Authorization: '.$token,
	    ];
        $variables = array(
			'query' => $suburb,
			'limit' => 10000
		);
        $query = '
        	query districtOptions($query: String!, $limit: Int!){
			    districtOptions(query: $query, limit: $limit){
			    key
			    value
			    provinceName
			    cityName
			    districtName
		    }
		}';

        $parameters = json_encode([
            'query' => $query,
            'variables' => json_encode($variables),
        ]);

        $result = $this->runApi($url, 'POST', $parameters, $headers);

        $districtName = '';
        $result = json_decode($result['response'], true);
        if (isset($result['data']['districtOptions']) && !empty($result['data']['districtOptions'])) {
            foreach ($result['data']['districtOptions'] as $option) {
                $district = explode('-',$option['districtName']);
                if (isset($district[1]) && isset($district[0])) {
                    if ((stripos($district[1],$suburb) !== false && stripos($district[0],$city) !== false) ||
                        (stripos($suburb,$district[1]) !== false && stripos($city,$district[0]) !== false)) {
                            $districtName = $option['districtName'];
                    }
                }
            }
        }
        return $districtName;
    }

    public function generateToken($generateToken)
    {
    	$clientId = $generateToken['clientId'];
    	$clientSecret = $generateToken['clientSecret'];
    	$url = $generateToken['tokenUrl'];

        # request
        $body = [
            'grant_type' => 'client_credentials'
        ];

        $headers = [
            'Content-Type:multipart/form-data',
            'Authorization: Basic '. base64_encode("$clientId:$clientSecret"),
        ];

        $callApiResult = $this->runApi($url, 'POST', $body, $headers);

        # get response
        $response = $callApiResult['response'];
        $err = $callApiResult['err'];
        $httpCode =  $callApiResult['httpCode'];
        $responseArray = json_decode($response, true);

        $accessToken = '';
        if (!empty($responseArray) && !empty($responseArray['access_token'])) {
            $accessToken = $responseArray['access_token'];
        }

        return $accessToken;
    }

    public function runApi($url, $method, $parameters = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($method == 'POST' || $method == 'PUT') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $result['response'] = $response;
        $result['err'] = $err;
        $result['httpCode'] =$httpCode;

		if ($this->getDebugMode()) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/logistix-generateawb-response.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($result);
        }

        return $result;
    }
}
