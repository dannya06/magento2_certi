<?php
namespace Smile\ElasticsuiteVirtualCategory\Model\Rule\Condition\Product;

/**
 * Interceptor class for @see \Smile\ElasticsuiteVirtualCategory\Model\Rule\Condition\Product
 */
class Interceptor extends \Smile\ElasticsuiteVirtualCategory\Model\Rule\Condition\Product implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Rule\Model\Condition\Context $context, \Magento\Backend\Helper\Data $backendData, \Magento\Eav\Model\Config $config, \Smile\ElasticsuiteCatalogRule\Model\Rule\Condition\Product\AttributeList $attributeList, \Smile\ElasticsuiteCatalogRule\Model\Rule\Condition\Product\QueryBuilder $queryBuilder, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Model\ResourceModel\Product $productResource, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection, \Magento\Framework\Locale\FormatInterface $localeFormat, \Smile\ElasticsuiteCatalogRule\Model\Rule\Condition\Product\SpecialAttributesProvider $specialAttributesProvider, \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory $queryFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $backendData, $config, $attributeList, $queryBuilder, $productFactory, $productRepository, $productResource, $attrSetCollection, $localeFormat, $specialAttributesProvider, $queryFactory, $data);
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
