<?php 
namespace Icube\PromoShipping\Plugin;
 
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;

class Converter
{
    protected $_priceHelper;

	public function __construct(
        ShippingMethodExtensionFactory $extensionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    )
    {
        $this->extensionFactory = $extensionFactory;
        $this->_priceHelper = $priceHelper;
    }

       public function aroundModelToDataObject(\Magento\Quote\Model\Cart\ShippingMethodConverter $subject, \Closure $proceed, $rateModel, $quoteCurrencyCode) 
       {    
   			$result = $proceed($rateModel, $quoteCurrencyCode);
            $extensibleAttribute =  ($result->getExtensionAttributes())
            ? $result->getExtensionAttributes()
            : $this->extensionFactory->create();
            
            $rateDescription = $rateModel->getMethodDescription()!=null ?$rateModel->getMethodDescription():"";
            if($rateDescription!=null){
                 //Estimation||Promoname||OriginalPrice
                $arrRateDescription = explode("||",$rateDescription);
                if(count($arrRateDescription)>1){
                    $extensibleAttribute->setEstimation($arrRateDescription[0]);
                    if(isset($arrRateDescription[1])){
                        $extensibleAttribute->setShippingPromoName($arrRateDescription[1]);
                    }
                    if(isset($arrRateDescription[2])){
                        $extensibleAttribute->setOriginalAmount($arrRateDescription[2]);
                    }
                }
            }
            $result->setExtensionAttributes($extensibleAttribute);

            return $result;
    }
}