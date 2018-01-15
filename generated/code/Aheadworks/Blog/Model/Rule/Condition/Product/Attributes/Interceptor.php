<?php
namespace Aheadworks\Blog\Model\Rule\Condition\Product\Attributes;

/**
 * Interceptor class for @see \Aheadworks\Blog\Model\Rule\Condition\Product\Attributes
 */
class Interceptor extends \Aheadworks\Blog\Model\Rule\Condition\Product\Attributes implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Rule\Model\Condition\Context $context, \Magento\Backend\Helper\Data $backendData, \Magento\Eav\Model\Config $config, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Model\ResourceModel\Product $productResource, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Framework\EntityManager\MetadataPool $metadataPool, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $backendData, $config, $productFactory, $productRepository, $productResource, $attrSetCollection, $localeFormat, $metadataPool, $scopeConfig, $data);
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
