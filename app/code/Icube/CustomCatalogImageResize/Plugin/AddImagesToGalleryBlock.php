<?php

namespace Icube\CustomCatalogImageResize\Plugin;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\MediaStorage\Service\ImageResize;

class AddImagesToGalleryBlock
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

    public function afterGetGalleryImages(Gallery $subject, $images) 
    {
        $enabled = $this->_scopeConfig->getValue(self::XML_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enabled == 1) {
            $product = $subject->getProduct();
            $galleryImages = $product->getMediaGalleryImages();
            if ($galleryImages) {
                foreach ($product->getMediaGalleryImages() as $image) {
                    $this->imageResize->resizeFromImageName($image->getFile());
                }
            }
            return $images;
        }
        return $images;
    }
}