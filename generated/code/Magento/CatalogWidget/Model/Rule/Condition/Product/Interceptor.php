<?php
namespace Magento\CatalogWidget\Model\Rule\Condition\Product;

/**
 * Interceptor class for @see \Magento\CatalogWidget\Model\Rule\Condition\Product
 */
class Interceptor extends \Magento\CatalogWidget\Model\Rule\Condition\Product implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Rule\Model\Condition\Context $context, \Magento\Backend\Helper\Data $backendData, \Magento\Eav\Model\Config $config, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Model\ResourceModel\Product $productResource, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Store\Model\StoreManagerInterface $storeManager, array $data = array(), \Magento\Catalog\Model\ProductCategoryList $categoryList = null)
    {
        $this->___init();
        parent::__construct($context, $backendData, $config, $productFactory, $productRepository, $productResource, $attrSetCollection, $localeFormat, $storeManager, $data, $categoryList);
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeOptions()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadAttributeOptions');
        if (!$pluginInfo) {
            return parent::loadAttributeOptions();
        } else {
            return $this->___callPlugins('loadAttributeOptions', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOperatorSelectOptions()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOperatorSelectOptions');
        if (!$pluginInfo) {
            return parent::getOperatorSelectOptions();
        } else {
            return $this->___callPlugins('getOperatorSelectOptions', func_get_args(), $pluginInfo);
        }
    }
}
