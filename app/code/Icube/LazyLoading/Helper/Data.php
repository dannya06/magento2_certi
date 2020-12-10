<?php

namespace Icube\LazyLoading\Helper;

class Data extends \WeltPixel\LazyLoading\Helper\Data
{
    public function getImageLoader()
    {
        $ImgLoader = $this->_assetRepo->getUrlWithParams('WeltPixel_LazyLoading::images/Loader.gif', []);
        $customImgLoader = $this->getIconUrl();
        if (!empty($customImgLoader)) {
            return $customImgLoader;
        }

        return $ImgLoader;
    }

    public function getSmallImageLoader($productId, $imageId, $width, $height)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);

        $smallImageUrl = $this->_assetRepo->getUrlWithParams('WeltPixel_LazyLoading::images/Loader.gif', []);
        if ($product) {
            $imageHelper  = $objectManager->get('Magento\Catalog\Helper\Image');
            $smallImageUrl = $imageHelper
                                ->init($product, $imageId)
                                ->constrainOnly(TRUE)
                                ->keepAspectRatio(TRUE)
                                ->keepTransparency(TRUE)
                                ->keepFrame(FALSE)
                                ->resize($width, $height);
        }

        return $smallImageUrl;
    }

    public function getImageLoaderIsSet()
    {
        if ($this->getLoadingPlaceholder()) {
            $customImgLoader = $this->getIconUrl();
            if (!empty($customImgLoader)) {
                return true;
            }
        }

        return false;
    }

    private function getIconUrl($storeId = 0)
    {
        $image = $this->getLoadingIcon($storeId);
        if ($image) {
            $imagePath = 'weltpixel/lazyloading/logo/' . $image;
            $imageUrl = $this->_currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            return $imageUrl . $imagePath;
        }

        return '';
    }

    private function getLoadingIcon($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_lazy_loading/advanced/loading_icon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_lazyLoadOptions['advanced']['loading_icon'];
        }
    }
}