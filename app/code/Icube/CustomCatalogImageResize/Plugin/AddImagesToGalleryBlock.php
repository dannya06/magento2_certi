<?php

namespace Icube\CustomCatalogImageResize\Plugin;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Icube\CustomCatalogImageResize\Service\ImageResize;
use Icube\CustomCatalogImageResize\Helper\Data;

class AddImagesToGalleryBlock
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
            foreach ($image->getMediaGalleryImages() as $key) {
                $product[] = $key->getFile();
            }
            $this->resize->resizeImage($product, $params);
            return $images;
        }
        return $images;
    }
}