<?php
namespace Smile\ElasticsuiteVirtualCategory\Model\Rule\Condition\Combine;

/**
 * Interceptor class for @see \Smile\ElasticsuiteVirtualCategory\Model\Rule\Condition\Combine
 */
class Interceptor extends \Smile\ElasticsuiteVirtualCategory\Model\Rule\Condition\Combine implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Rule\Model\Condition\Context $context, \Smile\ElasticsuiteCatalogRule\Model\Rule\Condition\ProductFactory $conditionFactory, \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory $queryFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $conditionFactory, $queryFactory, $data);
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
