<?php

namespace Icube\CustomCatalogImageResize\Plugin;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Icube\CustomCatalogImageResize\Service\ImageResize;
use Icube\CustomCatalogImageResize\Helper\Data;

class AddImagesToGalleryBlock
{
    const XML_ENABLE = 'icube_image_resize/general/enable';

    protected $imageResize;
    protected $resize;
    protected $product;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Product $product,
        ImageResize $imageResize,
        Data $resize
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->imageResize = $imageResize;
        $this->product = $product;
        $this->resize = $resize;
    }

    public function afterGetGalleryImages(Gallery $subject, $images) 
    {
        $enabled = $this->_scopeConfig->getValue(self::XML_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enabled == 1) {
            $image = $subject->getProduct();
            $paramsAll = $this->imageResize->getViewImages($this->imageResize->getThemesInUse());
            $params = array_filter($paramsAll, function($elem) {
                $image_id = $elem['id'];
                return !preg_match("/category/", $image_id);
            }); 
            $product = array();
            if ($image->getTypeId() == 'configurable') {
                $_children = $image->getTypeInstance()->getUsedProducts($image);
                foreach ($_children as $child){
                    foreach ($child->getMediaGalleryImages() as $imageChild) {
                        if (!$imageChild->getFile() == '') {
                            $product[] = $imageChild->getFile();
                        }
                    }
                }
            }

            foreach ($image->getMediaGalleryImages() as $key) {
                if (!$key->getFile() == '') {                    
                    $product[] = $key->getFile();
                }
            }

            $relatedProducts = $image->getRelatedProducts();
            if (!empty($relatedProducts)) {  
                foreach ($relatedProducts as $relatedProduct) {
                    $_product = $this->product->load($relatedProduct->getId());
                    if (!$_product->getThumbnail() == '') {
                        $product[] = $_product->getThumbnail();
                    }
                }
            }  
            
            $this->resize->resizeImage(array_unique($product), $params);
            return $images;
        }
        return $images;
    }
}