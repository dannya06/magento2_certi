<?php
namespace WeltPixel\OwlCarouselSlider\Block\Slider\Category;

/**
 * Interceptor class for @see \WeltPixel\OwlCarouselSlider\Block\Slider\Category
 */
class Interceptor extends \WeltPixel\OwlCarouselSlider\Block\Slider\Category implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \WeltPixel\OwlCarouselSlider\Helper\Products $helperProducts, \WeltPixel\OwlCarouselSlider\Helper\Custom $helperCustom, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory, \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportsCollectionFactory, \Magento\Reports\Block\Product\Widget\Viewed\Proxy $viewedProductsBlock, \Magento\Catalog\Model\CategoryFactory $categoryFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $helperProducts, $helperCustom, $catalogProductVisibility, $productsCollectionFactory, $reportsCollectionFactory, $viewedProductsBlock, $categoryFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage($product, $imageId, $attributes = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImage');
        if (!$pluginInfo) {
            return parent::getImage($product, $imageId, $attributes);
        } else {
            return $this->___callPlugins('getImage', func_get_args(), $pluginInfo);
        }
    }
}
