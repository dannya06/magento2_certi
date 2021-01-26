<?php

namespace Icube\CartRuleBanner\Block;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Banners implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if ($element->getRule() && $element->getRule()->getBanners()) {
            return $element->getRule()->getBanners()->asHtmlRecursive();
        }
        return '';
    }
}
