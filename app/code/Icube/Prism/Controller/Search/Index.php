<?php
namespace Icube\Prism\Controller\Search;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Magento\Framework\Module\Dir\Reader;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $storeManager;
    protected $helper;
    protected $message = '';

        /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Action\Context $context,
        \Icube\Prism\Helper\Data $helper
    )
    {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
      $token = $this->helper->_getToken();
// echo 'token:'.$token;exit();
      $result = $this->_searchProduct($token,'conf product');
// echo 'here';exit();
      $result = json_decode($result,true);
      echo print_r($result,true);exit();

      // $cartId = $this->_createCart($token,$output,$data[$i][0]);
      // $result = json_decode($cartId,true);

      // $productToCartResult = $this->_addProductToCart($productData,$cartId,$token);
      // $result = json_decode($productToCartResult,true);

      // //add shipping billing Information
      // $shippingData =  array(
      //   "addressInformation" => array(
      //     "shippingAddress" => array(
      //       "region" => $data[$i][8],
      //       "region_id" => 0,
      //       "country_id" => $data[$i][10],
      //       "street" => array(
      //         0 => $data[$i][6]
      //       ),
      //       "company" => $data[$i][5] ,
      //       'telephone' => $data[$i][11],
      //       'postcode' => $data[$i][9],
      //       "city" => $data[$i][7],
      //       "fax" => $data[$i][12],
      //       "firstname" => $data[$i][3],
      //       "lastname" => $data[$i][4],
      //       "email" => $data[$i][2],
      //       "region_code"=> "",
      //       "sameAsBilling"=> 0
      //     ),
      //     'billingAddress' => array(
      //        'region' => $dealerBillingAddress->getRegion(),
      //        'region_id' => $dealerBillingAddress->getRegionId(),
      //        'country_id' => $dealerBillingAddress->getCountry(),
      //        'street' => array(
      //         0 => $dealerStreet[0]
      //         ),
      //        'company' => $dealerBillingAddress->getCompany(),
      //        'telephone' => $dealerBillingAddress->getTelephone(),
      //        'postcode' => $dealerBillingAddress->getPostcode(),
      //        'city' => $dealerBillingAddress->getcity(),
      //        'firstname' => $dealerBillingAddress->getFirstname(),
      //        'lastname' => $dealerBillingAddress->getLastname(),
      //        'email' => $dealer->getEmail(),
      //        'region_code' => '',
      //     ),
      //      'shipping_method_code' => '',
      //      'shipping_carrier_code' => '',
      //   )
      // );
      // $shippingInfoResult = $this->_setShippBillInfo($cartId,$shippingData,$token);

      // $listShipping = $this->_getlistShipping($cartId,$token);
    }

    private function _searchProduct($token,$searchKey) {
      try {
        $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/products?searchCriteria[filter_groups][0][filters][0][field]=name&searchCriteria[filter_groups][0][filters][0][value]=%".$searchKey."%&searchCriteria[filter_groups][0][filters][0][condition_type]=like&searchCriteria[current_page]=1&searchCriteria[page_size]=2");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));

        $result = curl_exec($ch);
        // echo print_r($result,true);exit();
        return $result;
      } catch (Exception $e) {
        return $e->getMessage();
      }
    }

    private function _createCart($token,$output,$customerId){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/customers/".$customerId."/carts");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

      $cartId = curl_exec($ch);
      return $cartId;
    }

    private function _addProductToCart($productData,$cartId,$token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/carts/".$cartId."/items");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

      return curl_exec($ch);
    }

    private function _setShippBillInfo($cartId,$shippingData,$token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/carts/".$cartId."/shipping-information");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($shippingData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

      return curl_exec($ch);
    }

    private function _createOrder($cartId,$paymentMethodData,$token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/carts/".$cartId."/order");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentMethodData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

      return curl_exec($ch);
    }

    private function _getlistShipping($cartId,$token){
      $ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/carts/".$cartId."/shipping-methods");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

      return curl_exec($ch);
    }
}

?>
