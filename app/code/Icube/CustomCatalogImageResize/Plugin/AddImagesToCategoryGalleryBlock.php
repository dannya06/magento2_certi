<?php

namespace Icube\CustomCatalogImageResize\Plugin;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Icube\CustomCatalogImageResize\Helper\Data;
use Icube\CustomCatalogImageResize\Service\ImageResize;

class AddImagesToCategoryGalleryBlock
{
    const XML_ENABLE = 'icube_image_resize/general/enable';

    protected $imageResize;
    protected $resize;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ImageResize $imageResize,
        Data $resize
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->imageResize = $imageResize;
        $this->resize = $resize;
    }

    public function afterGetProductDetailsHtml(ListProduct $subject, $result, Product $product) 
    {
        $enabled = $this->_scopeConfig->getValue(self::XML_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enabled == 1) {
            $arrImage = array($product->getImage());
            $paramsAll = $this->imageResize->getViewImages($this->imageResize->getThemesInUse());
            $params = array_filter($paramsAll, function($elem) {
                $image_id = $elem['id'];
                return preg_match("/category/", $image_id);
            }); 
            foreach ($arrImage as $image) {
                $arrNew[] = $image;
                $this->resize->resizeImage($arrNew, $params);
            }
            return $result;
        }
        return $result;
    }
}