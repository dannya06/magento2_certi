<?php 
namespace Icube\PromoShipping\Plugin;
 
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;



class Converter
{
	public function __construct(ShippingMethodExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

       public function aroundModelToDataObject(\Magento\Quote\Model\Cart\ShippingMethodConverter $subject, \Closure $proceed, $rateModel, $quoteCurrencyCode) 
       {    
   			$result = $proceed($rateModel, $quoteCurrencyCode);
            $extensibleAttribute =  ($result->getExtensionAttributes())
            ? $result->getExtensionAttributes()
            : $this->extensionFactory->create();

            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $priceHelper = $om->get("\Magento\Framework\Pricing\Helper\Data");

            $rateDescription = $rateModel->getMethodDescription()!=null ?$rateModel->getMethodDescription():"";
            if($rateDescription!=null){
                 //Estimation||Promoname||OriginalPrice
                $arrRateDescription = explode("||",$rateDescription);
                if(count($arrRateDescription)>1){
                    $extensibleAttribute->setEstimation($arrRateDescription[0]);
                    $promoName ="";
                    if(isset($arrRateDescription[1])){
                        $promoName = $arrRateDescription[1];
                    }
                    $extensibleAttribute->setShippingPromoName($promoName);
                    
                    $oriPrice = "";
                    if(isset($arrRateDescription[2])){
                        $formattedCurrencyValue = $priceHelper->currency($arrRateDescription[2],true,false);
                        $oriPrice = $formattedCurrencyValue;
                    }
                    $extensibleAttribute->setShippingOriginalPrice($oriPrice);
                }
                
            }
            $result->setExtensionAttributes($extensibleAttribute);

            return $result;
    }
}