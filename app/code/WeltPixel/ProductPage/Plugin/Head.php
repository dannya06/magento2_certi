<?php
namespace WeltPixel\ProductPage\Plugin;

class Head {
    
    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;

    /**
     * var \WeltPixel\ProductPage\Helper\Data
     */
    protected $_helper;

    /**
     * Head constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\ProductPage\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\ProductPage\Helper\Data $helper
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
    }

    public function aroundInterpret(
        \Magento\Framework\View\Page\Config\Reader\Head $subject, 
        \Closure $proceed, 
        \Magento\Framework\View\Layout\Reader\Context $readerContext,
        \Magento\Framework\View\Layout\Element $headElement)
    {
        
        $result = $proceed($readerContext, $headElement);
        $pageConfigStructure = $readerContext->getPageConfigStructure();
        
        $store = $this->_storeManager->getStore();

        $categoryStoreCss = 'weltpixel_product_store_' . $store->getData('code') . '.css';

        $node = new \Magento\Framework\View\Layout\Element('<css src="WeltPixel_ProductPage::css/'. $categoryStoreCss .'" />');
        $node->addAttribute('content_type', 'css');
        $pageConfigStructure->addAssets($node->getAttribute('src'), $this->getAttributes($node));

        return $result;
    }
    
    
    protected function getAttributes($element)
    {
        $attributes = [];
        foreach ($element->attributes() as $attrName => $attrValue) {
            $attributes[$attrName] = (string)$attrValue;
        }
        return $attributes;
    }
}