<?php
namespace Smile\ElasticsuiteVirtualCategory\Model\Category\Attribute\Backend\VirtualRule;

/**
 * Interceptor class for @see \Smile\ElasticsuiteVirtualCategory\Model\Category\Attribute\Backend\VirtualRule
 */
class Interceptor extends \Smile\ElasticsuiteVirtualCategory\Model\Category\Attribute\Backend\VirtualRule implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Smile\ElasticsuiteCatalogRule\Model\RuleFactory $ruleFactory)
    {
        $this->___init();
        parent::__construct($ruleFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        if (!$pluginInfo) {
            return parent::validate($object);
        } else {
            return $this->___callPlugins('validate', func_get_args(), $pluginInfo);
        }
    }
}
