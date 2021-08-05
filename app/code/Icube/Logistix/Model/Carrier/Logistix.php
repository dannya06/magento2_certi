<?php

namespace Icube\Logistix\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;

class Logistix extends AbstractCarrier implements CarrierInterface
{
    protected $_code = 'logistix';

    protected $_rateResultFactory;

    protected $_rateMethodFactory;

    protected $_icubeshipmapFactory;

    protected $_icubeshipcachehelper;
	
    private $logger;
    protected $_shipmentOrder;
	protected $_product;
    protected $_orderFactory;
    protected $_objectManager;
    protected $_quote;
    protected $_moduleManager;
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Icube\ShippingBase\Model\IcubeShipMapFactory $icubeshipmaptFactory,
        \Icube\ShippingBase\Helper\Cache $icubeshipcachehelper,
        \Icube\Logistix\Model\ResourceModel\ShipmentQueue $shipmentOrder,
        \Magento\Catalog\Model\Product $product,
        \Magento\Sales\Model\Order $orderFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Checkout\Model\Cart $quote,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_icubeshipmapFactory = $icubeshipmaptFactory;
        $this->_icubeshipcachehelper = $icubeshipcachehelper;
        $this->logger = $logger;
        $this->_shipmentOrder = $shipmentOrder;
        $this->_product = $product;
        $this->_orderFactory = $orderFactory;
        $this->_objectManager = $objectManager;
        $this->_quote = $quote;
        $this->_moduleManager = $moduleManager;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');
        $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);
        return $shippingPrice;
    }

    public function getDebugMode()
    {
        $value = $this->getConfigData("debug");
        return $value;
    }

    /**
     * @return float
     */
    private function getShippingFromApi($request)
    {
        $prodUrl = $this->getConfigData("production_url");
        $sandboxUrl = $this->getConfigData("sandbox_url");
        $sandBoxFlag = $this->getConfigData("use_sandbox");
        $clientId = $this->getConfigData("client_id");
        $clientSecret = $this->getConfigData("client_secret");
        $tokenUrl = $this->getConfigData("token_url");
        $originCoord = $this->getConfigData("origin_coord");
        $originId = $this->getConfigData("origin_id");
        $excludedMethod = $this->getConfigData("exclude_method_option");
        $insurance = $this->getConfigData("insurance");

        $generateToken = array(
            "clientId" => $clientId,
            "clientSecret" => $clientSecret,
            "tokenUrl" => $tokenUrl
        );

        $packWeight = $request->getPackageWeight();
        $destinationCoord = null;
        $quote = $this->_quote->getQuote();
        if ($quote->getDestLatitude() != null && $quote->getDestLongitude() != null) {
            $destinationCoord = $quote->getDestLatitude().','.$quote->getDestLongitude();
        }

        if ($packWeight == "" || $packWeight == NULL) {
            $packWeight = 0;
        }

        $packWeight = $packWeight * 1000;
        if ($packWeight <= 1000) {
            $packWeight = 1000;
        }
        
        if ($sandBoxFlag == 1) {
            $url = $sandboxUrl;
        } else {
            $url = $prodUrl;
        }

        $shippingList = [];

        if ($request->getDestCity() != "") {
            $destCity = $request->getDestCity();
            $destinationId = "";
            $shipMap = $this->_icubeshipmapFactory->create();
            $collection = $shipMap->getCollection();
            $collection
                ->addFieldToFilter('main_table.city', array('eq' => $destCity))
                ->addFieldToFilter('main_table.rule', array('eq' => 'area'))
                ->addFieldToFilter('main_table.target_ship_method', array('eq' => 'logistix'));
            foreach($collection as $item){
                $data = $item->getData();
                $destinationId = $data["target_city"];
            }

            // check cache exist
            $continueProcess = 0;
            $cacheId = $this->_icubeshipcachehelper->getId("logistix_".$originId."_".$destinationId."_".$packWeight);

            if($this->_icubeshipcachehelper->load($cacheId)){
                $strShippingList = $this->_icubeshipcachehelper->load($cacheId);
                $arrShippingList = json_decode($strShippingList,1);
                if (!isset($arrShippingList)) {
                    $continueProcess = 1;
                } else {
                    return $arrShippingList;
                }
            } else {
                $continueProcess = 1;
            }
            
            if ($continueProcess == 1) {
                $totalLength = 0;
                $totalWidth = 0;
                $totalHeight = 0;
                foreach ($request->getAllItems() as $item) {
                    $dataProduct = $this->_product->load($item->getProduct()->getId());           
                    $length = (int)$dataProduct->getData('dimension_package_length');
                    $width = (int)$dataProduct->getData('dimension_package_width');
                    $height = (int)$dataProduct->getData('dimension_package_height');
                    if ($length && $width && $height) {
                        $totalLength += $length;
                        $totalWidth += $width;
                        $totalHeight += $height;
                    } else {
                        $totalLength += 1;
                        $totalWidth += 1;
                        $totalHeight += 1;
                    }
                }

                $variables = '{
                  "input": {
                    "fromCode": "'.$originId.'",
                    "fromLatlong": "'.$originCoord.'",
                    "toCode": "'.$destinationId.'",
                    "toLatlong": "'.$destinationCoord.'",
                    "package": {
                      "weight": '.$packWeight.',
                      "dimension": {
                        "height": '.$totalHeight.',
                        "length": '.$totalLength.',
                        "width": '.$totalWidth.'
                      }
                    },
                    "providerFilters": [
                      {
                        "providerName": "JNT",
                        "servicesName": [
                          "EZ"
                        ]
                      },
                      {
                        "providerName": "POS",
                        "servicesName": [
                          "PAKET KILAT KHUSUS"
                        ]
                      },
                      {
                        "providerName": "JNE",
                        "servicesName": [
                          "YES",
                          "REG"
                        ]
                      },
                      {
                        "providerName": "GRAB",
                        "servicesName": [
                          "INSTANT",
                          "SAMEDAY"
                        ]
                      },
                      {
                        "providerName": "GOSEND",
                        "servicesName": [
                          "INSTANT",
                          "SAMEDAY"
                        ]
                      },
                      {
                        "providerName": "SICEPAT",
                        "servicesName": [
                          "GOKIL",
                          "BEST",
                          "REG"
                        ]
                      }
                    ]
                  }
                }';

                $query = '
                    query shippingRate($input: ShippingRateInput){
                        shippingRate(input: $input){
                            provider
                            service
                            message
                            isEnable
                            cost {
                                value
                                code
                            }
                        }
                    }';

                $token = $this->generateToken($generateToken);
                $headers = [
                    'Content-Type: application/json',
                    'Authorization: '.$token,
                ];
                $parameters = json_encode([
                    'query' => $query,
                    'variables' => $variables,
                ]);

                if ($this->getDebugMode()) {
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/logistix-rate-request-parameters.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info(json_decode($parameters, true));
                }

                if ($destCity != "") {
                    try {
                        $response = $this->runApi($url, 'POST', $parameters, $headers);
                        if ($response["httpCode"] == 200) {
                            if ($response["response"]) {
                                $rateLogistic = json_decode($response["response"], true);
                                foreach ($rateLogistic['data']['shippingRate'] as $type) {
                                    if (strpos($excludedMethod, $type["service"]." ".$type["provider"]) === false) {
                                        if ($type["cost"]['value'] != 0) {
                                            $shippingList[] = array(
                                                "name" => $type["provider"],
                                                "rate_name" => $type["service"],
                                                "finalRate" => $type["cost"]['value'],
                                                "rate_id" => $type["provider"].$type["service"],
                                                "dest_id" => $destinationId
                                            );
                                        }
                                    }
                                }
                            }
                        } else {
                            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/logistix-error.log');
                            $logger = new \Zend\Log\Logger();
                            $logger->addWriter($writer);
                            $logger->info("Process Failed. API response : ".$response);
                        }
                    } catch (\Exception $e) {
                        $this->logger->critical($e->getMessage());
                    }
                }
                $this->_icubeshipcachehelper->save(json_encode($shippingList),$cacheId);
            }
        }

        return $shippingList;    
    }


    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $request = $this->excludeVirtual($request);
        $request = $this->excludeDownloadable($request);
        $request = $this->freeShippingByQty($request);

        $listShipping = $this->getShippingFromApi($request);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        
        $methodOption = $this->getConfigData("method_option");
        $methodSelected = $this->getConfigData("specific_method");
        
        $freeShipOption = $this->getConfigData("allow_free");
        $freeShipMethod = $this->getConfigData("specific_method_free");
        $freeShipAmount = $this->getConfigData("min_freeshipping_amount");

        $arrFreeShipping = array(
            "option"=>$freeShipOption,
            "method"=>$freeShipMethod,
            "min_free" => $freeShipAmount
        );

        $arrMethodSelected = explode(",",$methodSelected);

        $result = $this->_rateResultFactory->create();

        if (!empty($listShipping)) {
            foreach ($listShipping as $key => $value) {
                if ($methodOption == 1) {
                    foreach ($arrMethodSelected as $idx => $val) {
                        if($listShipping[$key]["name"] == $arrMethodSelected[$idx]){
                            $method = $this->setMethod($listShipping[$key],$arrFreeShipping, $request);
                            $result->append($method);
                            break;
                        }
                    }
                } else {
                    $method = $this->setMethod($listShipping[$key],$arrFreeShipping, $request);
                    $result->append($method);
                }
            }
        }
        
        return $result;   
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
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/logistix-rate-response.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($result);
        }

        return $result;
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

    private function excludeVirtual($request)
    {
        if (!$this->getConfigFlag('use_virtual_product') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->getTypeId() == 'virtual') {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        return $request;
    }

    private function excludeDownloadable($request)
    {
        if (!$this->getConfigFlag('use_download_product') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }                
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {       
                        if ($child->getProduct()->getTypeId()=='downloadable') {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->getTypeId()=='downloadable') {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        return $request;
    }

    private function setMethod($shipping, $arrFreeShipping, $request)
    {
        $insurance = $this->getConfigData("insurance");

        $method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);

        $shipping["dest_id"] = str_replace("-", " ", $shipping["dest_id"]);

        $carrierTitle = $shipping["name"];
        if ($insurance == true) {
            $carrierTitle = $carrierTitle . " + insurance";
        }
        $method->setCarrierTitle($carrierTitle);
        $method->setMethod($shipping["name"]."-".$shipping["rate_name"]."-".$shipping["dest_id"]."-".$shipping["rate_id"]);
        $method->setMethodTitle($shipping["rate_name"]);
        
        $arrFreeMethodSelected = explode(",",$arrFreeShipping["method"]);

         /**
         * Custom Icube For handle promo ongkir
         */
        if ($this->_moduleManager->isEnabled('Icube_PromoShipping')) {
            $newCalculate = $request->customGetFreeShipping();
            $newCalculate = json_decode($newCalculate,1);
        }else{
            $newCalculate = [];
        }

        if ($request->getFreeShipping() == true || (($arrFreeShipping["option"] == 1 && $request->getPackageValue() > $arrFreeShipping["min_free"]) && ($arrFreeShipping["min_free"] > 0 && isset($arrFreeShipping["min_free"]))))
        {
            /**
             * Custom Icube For handle promo
             */
            foreach ($arrFreeMethodSelected as $k => $v) {
                if ($shipping["name"] == $v) {
                    if (count($newCalculate) > 0) {
                        foreach ($newCalculate as $keyCalc => $valCalc) {
                            if ($newCalculate[$keyCalc]["ruleAction"] == "shipping_disc_by_amount") {
                                if ($newCalculate[$keyCalc]["discAmount"] > 0 && $newCalculate[$keyCalc]["discAmount"] != "") {
                                    if ($newCalculate[$keyCalc]["discAmount"] > $shipping["finalRate"]) {
                                        $method->setPrice(0);
                                        $method->setCost(0);    
                                    } else {
                                        $method->setPrice($shipping["finalRate"] - $newCalculate[$keyCalc]["discAmount"]);
                                        $method->setCost($shipping["finalRate"] - $newCalculate[$keyCalc]["discAmount"]);
                                    }
                                    if(isset($shipping["min_day"]) && isset($shipping["max_day"])) {
                                        $method->setMethodDescription($newCalculate[$keyCalc]["ruleName"]."||".$shipping["finalRate"]);
                                    }
                                } else {
                                    $method->setPrice(0);
                                    $method->setCost(0);
                                }
                            } elseif ($newCalculate[$keyCalc]["ruleAction"]=="shipping_disc_by_percent") {
                                if ($newCalculate[$keyCalc]["discAmount"] > 0 && $newCalculate[$keyCalc]["discAmount"] != "") {
                                    $newPricePercentage = ($newCalculate[$keyCalc]["discAmount"]/100) * $shipping["finalRate"];
                                    if ($newCalculate[$keyCalc]["maxDiscAmount"] > 0 && $newCalculate[$keyCalc]["maxDiscAmount"] != "") {
                                        if ($newPricePercentage > $newCalculate[$keyCalc]["maxDiscAmount"]){
                                            $method->setPrice($shipping["finalRate"] - $newCalculate[$keyCalc]["maxDiscAmount"]);
                                            $method->setCost($shipping["finalRate"] - $newCalculate[$keyCalc]["maxDiscAmount"]);
                                        } else {
                                            $method->setPrice($shipping["finalRate"] - $newPricePercentage);
                                            $method->setCost($shipping["finalRate"] - $newPricePercentage);
                                            $method->setMethodTitle($shipping["rate_name"]);
                                        }
                                        if(isset($shipping["min_day"]) && isset($shipping["max_day"])) {
                                            $method->setMethodDescription($newCalculate[$keyCalc]["ruleName"]."||".$shipping["finalRate"]);
                                        }
                                    } else {
                                        $method->setPrice($shipping["finalRate"] - $newPricePercentage);
                                        $method->setCost($shipping["finalRate"] - $newPricePercentage);
                                        $method->setMethodTitle($shipping["rate_name"]." (".$newCalculate[$keyCalc]["ruleName"].")");
                                    }
                                    if(isset($shipping["min_day"]) && isset($shipping["max_day"])) {
                                        $method->setMethodDescription($newCalculate[$keyCalc]["ruleName"]."||".$shipping["finalRate"]);
                                    }
                                } else {
                                    $method->setPrice(0);
                                    $method->setCost(0);
                                }
                            }
                        }
                    } else {
                        $method->setPrice(0);
                        $method->setCost(0);
                    }
                    break;
                } else {
                    $method->setPrice($shipping["finalRate"]);
                    $method->setCost($shipping["finalRate"]);
                }
            }
        } else {
            $method->setPrice($shipping["finalRate"]);
            $method->setCost($shipping["finalRate"]);
        }

        return $method;
    }

    /**
     * @param RateRequest $request
     * @return RateRequest
     */
    private function freeShippingByQty($request)
    {
        $oldValue = $request->getPackageValue();
        if ($request->getAllItems()) {
            $freePackageValue = 0;
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freePackageValue += $item->getBaseRowTotal();
                }
            }
            $oldValue = $request->getPackageValue();
            if (!$this->_moduleManager->isEnabled('Icube_PromoShipping')) {
                $request->setPackageValue($oldValue - $freePackageValue);
            }
        }
        return $request;
    }
}
