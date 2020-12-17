<?php

namespace Icube\LazyLoadingWebp\Block\Product;

class Image extends \Magento\Catalog\Block\Product\Image
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Jajuma\WebpImages\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $data['template']='Icube_LazyLoadingWebp::category/lazyload_custom.phtml';
        if (isset($data['template'])) {
            $this->setTemplate($data['template']);
            unset($data['template']);
        }
        parent::__construct($context, $data);
    }

    public function getImageUrl()
    {
        $imageUrl = parent::getImageUrl();

        if ($imageUrl != '') {
            return $this->helper->convert($imageUrl);
        } else {
            return $imageUrl;
        }
    }

    public function getHoverImageUrl()
    {
        $hoverImageUrl = parent::getHoverImageUrl();
        if ($hoverImageUrl != '') {
            return $this->helper->convert($hoverImageUrl);
        } else {
            return $hoverImageUrl;
        }
    }

    public function getOrigImageUrl()
    {
        return parent::getImageUrl();
    }
}
