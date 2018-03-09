<?php
namespace Icube\Prism\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
 
class Data extends AbstractHelper
{
    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->storeManager = $storeManager;
    }

    public function _getToken(){
		//login and get token
		//hardcoded username + password
		$userData = array("username" => "PRISM", "password" => "PRISM4321");
		$ch = curl_init($this->storeManager->getStore()->getBaseUrl()."index.php/rest/V1/integration/admin/token");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import_order.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);

		$token = curl_exec($ch);
		$result = json_decode($token,true);
		if(isset($result['message'])){
			$logger->info('Error : '.$result['message']);
			$message = $result['message'];
			$error = true;
			exit;
		}else{
			return $result;
		}
    }
}