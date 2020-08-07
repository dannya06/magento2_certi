<?php

namespace Icube\CustomCatalogImageResize\Plugin;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\MediaStorage\Service\ImageResize;

class AddImagesToCategoryGalleryBlock
{
    const XML_ENABLE = 'icube_image_resize/general/enable';

    protected $imageResize;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ImageResize $imageResize
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->imageResize = $imageResize;
    }

    public function afterGetProductDetailsHtml(ListProduct $subject, $result, Product $product) 
    {
        $enabled = $this->_scopeConfig->getValue(self::XML_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enabled == 1) {
            $arrImage = array($product->getImage());
            foreach ($arrImage as $image) {
                 $this->imageResize->resizeFromImageName($image);
            }
            return $result;
        }
        return $result;
    }
}