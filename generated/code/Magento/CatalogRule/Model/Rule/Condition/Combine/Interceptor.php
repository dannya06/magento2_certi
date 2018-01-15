<?php
namespace Magento\CatalogRule\Model\Rule\Condition\Combine;

/**
 * Interceptor class for @see \Magento\CatalogRule\Model\Rule\Condition\Combine
 */
class Interceptor extends \Magento\CatalogRule\Model\Rule\Condition\Combine implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Rule\Model\Condition\Context $context, \Magento\CatalogRule\Model\Rule\Condition\ProductFactory $conditionFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $conditionFactory, $data);
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
