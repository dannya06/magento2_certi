<?php
namespace Icube\CheckCity\Plugin;
use Magento\Checkout\Model\PaymentInformationManagement as CheckoutPaymentInformationManagement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Psr\Log\LoggerInterface;
use \Magento\Checkout\Model\Cart;
/**
 * Class PaymentInformationManagement
 */
class PaymentInformationManagement
{
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var MethodList
     */
    private $methodList;
    /**
     * @var bool
     */
    private $checkMethods;
    
    private $quote;
    private $url;
    private $responseFactory;
    /**
     * PaymentInformationManagement constructor.
     * @param CartManagementInterface $cartManagement
     * @param LoggerInterface $logger
     * @param MethodList $methodList
     * @param bool $checkMethods
     */
    public function __construct(
        CartManagementInterface $cartManagement,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        LoggerInterface $logger,
        \Magento\Framework\UrlInterface $url,        
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResourceConnection $resourceCon,
        Cart $quote,
        $checkMethods = false
    ) {
        $this->responseFactory = $responseFactory;
        $this->cartManagement = $cartManagement;
        $this->logger = $logger;
        $this->quote = $quote;
        $this->url = $url;
        $this->resourceCon    = $resourceCon;
        $this->checkMethods = $checkMethods;
        $this->messageManager = $messageManager;
    }
    /**
     * @param CheckoutPaymentInformationManagement $subject
     * @param \Closure $proceed
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return int
     * @throws CouldNotSaveException
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        CheckoutPaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $quote = $this->quote->getQuote();
        $connection = $this->resourceCon->getConnection();
        $city = $quote->getShippingAddress()->getCity();
        $city = explode(',', $city);
        if(sizeof($city) == 1) {
            $kota       = ltrim($city[0]);
            $kecamatan  = ltrim($city[0]);
            $kelurahan  = ltrim($city[0]);
        } else if(sizeof($city) == 2) {
            $kota       = ltrim($city[0]);
            $kecamatan  = ltrim($city[1]);
            $kelurahan  = ltrim($city[1]);
        } else {            
            $kota       = ltrim($city[0]);
            $kecamatan  = ltrim($city[1]);
            $kelurahan  = ltrim($city[2]);
        }
        $sql = "SELECT  * FROM city where city = '".$kota."' and kecamatan ='".$kecamatan.", ".$kelurahan."'";
        $result = $connection->fetchAll($sql);
        if (count($result) == 0) {
            $redirectionUrl = $this->url->getUrl('customer/address/edit/id/'.$quote->getShippingAddress()->getCustomerAddressId());
            throw new CouldNotSaveException(
                __('City '.$quote->getShippingAddress()->getCity().' could not be found. Please try edit you shipping address.')
                );
            exit();
        }
        $subject->savePaymentInformation($cartId, $paymentMethod, $billingAddress);
        try {
            $orderId = $this->cartManagement->placeOrder($cartId);
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new CouldNotSaveException(
                __('An error occurred on the server. Please try to place the order again.'),
                $exception
            );
        }
        return $orderId;
    }
}
