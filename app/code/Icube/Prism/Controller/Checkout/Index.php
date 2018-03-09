<?php
namespace Icube\Prism\Controller\Checkout;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Magento\Framework\Module\Dir\Reader;
use Magento\Quote\Model\QuoteIdMaskFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    const PAYMENT_MAPP = array('transfer'=>'banktransfer', 'cod'=>'cashondelivery', 'vt_web'=>'snap');

    protected $storeManager;
    protected $helper;
    protected $message = '';

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    protected $_countryFactory;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Action\Context $context,
        \Icube\Prism\Helper\Data $helper,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Sales\Model\Order $order
    )
    {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_countryFactory = $countryFactory;
        $this->order = $order;
        parent::__construct($context);
    }

    public function execute()
    {
      if($_SERVER['REQUEST_METHOD'] === 'POST'){
      
        $token = $this->helper->_getToken();
        $shippsArr=array();

        $data = json_decode(file_get_contents('php://input'),true);
        try{

            $products = $data['data']['order']['line_items'];
            $countryid = '';
            if($data['data']['shipment']['provider']['custom']['country']=='IDN'){
                $countryModel = $this->_countryFactory->create()->loadByCode('ID');
                $countryid = $countryModel->getId();
            }else {
              $countryid =$data['data']['shipment']['provider']['custom']['country'];
            }

            // extract quote id
            $partsString = $data['data']['shipment']['choice']['id'];
            $parts = explode('_', $partsString);
            $QuoteId = array_pop($parts);
            $shippingMethodCode = array_pop($parts);
            $shippingCarrierCode = array_pop($parts);

            $customerEmail = $data['data']['buyer']['email'];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerModel = $objectManager->create('Magento\Customer\Model\Customer');
            $customerModel->setWebsiteId(1);
            $customerModel->loadByEmail($customerEmail);
            $customerId = $customerModel->getId();
            if(!is_null($customerId)) {
              $quoteObject = $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->load($QuoteId);
              $quoteObject->setCustomerId($customerId)
                ->setCustomerEmail($customerEmail)
                ->setCustomerFirstname($data['data']['buyer']['name'])
                ->setCustomerLastname($data['data']['buyer']['name'])
                ->setCustomerGroupId($customerModel->getGroupId())
                ->save();
            }
            
            //get and add shipping billing Information
            $shippingData =  array(
              "addressInformation" => array(
                "shipping_address" => array(
                  "region" => $data['data']['shipment']['provider']['custom']['first_level'],
                  "region_id" => $data['data']['shipment']['provider']['custom']['region_id'],
                  "country_id" => $countryid,
                  "street" => array(
                    0 => $data['data']['shipment']['info']['address']
                  ),
                  'telephone' => $data['data']['buyer']['phone_number'],
                  'postcode' => $data['data']['shipment']['info']['postalCode'],
                  "city" => $data['data']['shipment']['provider']['custom']['second_level']."/".$data['data']['shipment']['provider']['custom']['third_level'],
                  "email" => $data['data']['buyer']['email'],
                  "firstname" => $data['data']['buyer']['name'],
                  "lastname" => $data['data']['buyer']['name'],
                  "region_code"=> $data['data']['shipment']['provider']['custom']['first_level']
                ),
                'billingAddress' => array(
                  "region" => $data['data']['shipment']['provider']['custom']['first_level'],
                  "region_id" => $data['data']['shipment']['provider']['custom']['region_id'],
                  "country_id" => $countryid,
                  "street" => array(
                    0 => $data['data']['shipment']['info']['address']
                  ),
                  'telephone' => $data['data']['buyer']['phone_number'],
                  'postcode' => $data['data']['shipment']['info']['postalCode'],
                  "city" => $data['data']['shipment']['provider']['custom']['second_level']."/".$data['data']['shipment']['provider']['custom']['third_level'],
                  "email" => $data['data']['buyer']['email'],
                  "firstname" => $data['data']['buyer']['name'],
                  "lastname" => $data['data']['buyer']['name'],
                  "region_code"=>  $data['data']['shipment']['provider']['custom']['first_level']
                ),
                'shipping_method_code' => $shippingMethodCode,
                'shipping_carrier_code' => $shippingCarrierCode
              )
            );
            
            $shippingInfoResult = $this->_setShippBillInfo($QuoteId,$shippingData,$token);

            $resultShipp= json_decode($shippingInfoResult,true);

            $method = $data['data']['payment']['provider']['type'];
            $method = self::PAYMENT_MAPP[$method];
            $paymentMethodData = array(
              'paymentMethod' => array(
                'method' => $method
              ),
            );

              $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
              $connection = $resource->getConnection();
              $tableName = $resource->getTableName('quote_id_mask'); 

              $query = "Select * FROM " . $tableName ." WHERE quote_id =" . $QuoteId;
              $results = $connection->fetchAll($query);

              $maskedId= $results[0]['masked_id'];

            $checkoutResult = $this->_createOrder($maskedId,$paymentMethodData,$token);
            $checkoutResult = json_decode($checkoutResult,true);

            $checkOrder = $this->order->load($checkoutResult);
            $notes = $data['data']['notes'];
            $checkOrder->addStatusHistoryComment($notes);
            $checkOrder->save();
            $orderId = $checkOrder->getIncrementId();

            $result = array(
                      'status'=>'success',
                      'data'=> array(
                          'invoice'=>array(
                              'id'=>$orderId,
                              // 'status'=>$checkOrder->getStatus(),
                              'status'=>'ISSUED' ,
                              'grand_total'=>array(
                              'currency_code'=>'IDR',
                              'amount'=>(string)abs($resultShipp['totals']['grand_total'])
                                    ),
                                  'line_items'=>$products,
                                  'shipment'=>array(
                                    'provider'=>$data['data']['shipment']['provider'],
                                    'choice'=>array(
                                      'id'=>$data['data']['shipment']['choice']['id'],
                                      'name'=>$data['data']['shipment']['choice']['name'],
                                      'cost'=>$data['data']['shipment']['choice']['cost']),
                                    'cost'=>$data['data']['shipment']['choice']['cost'],
                                    'info'=>$data['data']['shipment']['info']
                                    ),
                                  'payment'=>$data['data']['payment']
                                  ),
                                ),
                        );

            echo json_encode($result);
      }
      catch (\Exception $e) {
            $error = array(
                'status'=>'error',
                'message'=> $e->getMessage(),
                );
            echo  json_encode($error);
        }
      }
    }

    private function _setShippBillInfo($QuoteId,$shippingData,$token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/carts/".$QuoteId."/shipping-information");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($shippingData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));

      return curl_exec($ch);
    }

    private function _createOrder($maskedId,$paymentMethodData,$token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/guest-carts/".$maskedId."/order");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentMethodData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " .$token));

      return curl_exec($ch);
    }
}

?>
