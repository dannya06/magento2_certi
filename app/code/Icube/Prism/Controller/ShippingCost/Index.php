<?php
namespace Icube\Prism\Controller\ShippingCost;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Magento\Framework\Module\Dir\Reader;
use Magento\Quote\Model\QuoteIdMaskFactory;

class Index extends \Magento\Framework\App\Action\Action
{
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
        \Magento\Directory\Model\CountryFactory $countryFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
      
          $token = $this->helper->_getToken();
          $shippsArr=array();

          $data = json_decode(file_get_contents('php://input'),true);

          $products = $data['data']['cart']['line_items'];
          $shipment = $data['data']['shipment_area'];
          $countryid = '';
            if($shipment['custom']['country']=='IDN'){
                $countryModel = $this->_countryFactory->create()->loadByCode('ID');
                $countryid = $countryModel->getId();
            }

          $maskedCartId = $this->_createCart($token);
          $result = json_decode($maskedCartId,true);

          $cart = $this->quoteIdMaskFactory->create()->load($result, 'masked_id');
          $QuoteId = $cart->getQuoteId();

          $productData = array();
          foreach($products as $paramProduct) {
            $productData = array(
              "cartItem" => array(
                "quote_id" => $QuoteId,
                "sku" => $paramProduct['product']['id'],
                "qty" => $paramProduct['quantity']
              )
            );
            $productToCartResult = $this->_addProductToCart($productData,$QuoteId,$token);
          }

          //add shipping billing Information
          $shippingData =  array(
            "addressInformation" => array(
              "shippingAddress" => array(
                "region" => $shipment['custom']['first_level'],
                "region_id" => $shipment['custom']['region_id'],
                "country_id" => $countryid,
                "street" => array(
                  0 => "dummy street 123"
                ),
                // "street" => ["dummy street 123"],//$data['data']['shipment']['info']['address'],
                'telephone' => '123123',//$data['data']['buyer']['phone_number'],
                'postcode' => '12312313',//$data['data']['shipment']['info']['postalCode'],
                "city" => $shipment['custom']['second_level']."/".$shipment['custom']['third_level'],
                "firstname" => 'dummy',//$data['data']['buyer']['name'],
                "lastname" => 'dummy',//$data['data']['buyer']['name'],
                "email" => 'dummy@dummy.com',
                "region_code"=> "12323",
                "sameAsBilling"=> 0

              ),
              'billingAddress' => array(
                "region" => $shipment['custom']['first_level'],
                "region_id" => $shipment['custom']['region_id'],
                "country_id" => $countryid,
                "street" => array(
                  0 => "dummy street 123"
                ),
                // "street" => ["dummy street 123"],//$data['data']['shipment']['info']['address'],
                'telephone' => '123123',//$data['data']['buyer']['phone_number'],
                'postcode' => '12312313',//$data['data']['shipment']['info']['postalCode'],
                "city" => $shipment['custom']['second_level']."/".$shipment['custom']['third_level'],
                "firstname" => 'dummy',//$data['data']['buyer']['name'],
                "lastname" => 'dummy',//$data['data']['buyer']['name'],
                "email" => 'dummy@dummy.com',
                "region_code"=> "12323"
              ),
               // 'shipping_method_code' => 'flatrate',
               // 'shipping_carrier_code' => 'flatrate_flatrate'
            )
          );
          
          $shippingInfoResult = $this->_setShippBillInfo($QuoteId,$shippingData,$token);

          $listShipping = $this->_getlistShipping($QuoteId,$token);
          $ShippResult = json_decode($listShipping,true);

          foreach ($ShippResult as $value) {
              $shippArr = array();
              $shippArr['id'] = $value['carrier_code'].'_'.$value['method_code'].'_'.$QuoteId;
              $shippArr['name'] = $value['carrier_title'];
              $shippArr['cost'] = array(
                                'currency_code'=>'IDR',
                                'amount'=>(string)abs($value['amount']),
                                );
              $shippsArr[] = $shippArr;
          }

          $result = array(
                    'status'=>'success',
                    'data'=>array(
                          'shipment_choices'=> $shippsArr,
                            )
                    );
          echo json_encode($result);
        }
    }

    private function _createCart($token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/guest-carts");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " .$token));
      $QuoteId = curl_exec($ch);

      return $QuoteId;
    }

    private function _addProductToCart($productData,$QuoteId,$token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/carts/".$QuoteId."/items");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));

      return curl_exec($ch);
    }

    private function _setShippBillInfo($QuoteId,$shippingData,$token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/carts/".$QuoteId."/shipping-information");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($shippingData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));

      return curl_exec($ch);
    }

    private function _getlistShipping($QuoteId,$token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/carts/".$QuoteId."/shipping-methods");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));

      return curl_exec($ch);
    }
}

?>
