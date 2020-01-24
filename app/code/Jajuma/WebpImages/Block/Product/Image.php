<?php

/**
 * Converting images on category & search pages
 * 
 * @author Icube Developer <fiko@icube.us>
 * @since 2.0.1
 */

namespace Jajuma\WebpImages\Block\Product;

class Image extends \Magento\Catalog\Block\Product\Image
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Jajuma\WebpImages\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
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
}
