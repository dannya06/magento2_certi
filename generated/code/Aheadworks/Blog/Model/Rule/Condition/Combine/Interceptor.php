<?php
namespace Aheadworks\Blog\Model\Rule\Condition\Combine;

/**
 * Interceptor class for @see \Aheadworks\Blog\Model\Rule\Condition\Combine
 */
class Interceptor extends \Aheadworks\Blog\Model\Rule\Condition\Combine implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Rule\Model\Condition\Context $context, \Aheadworks\Blog\Model\Rule\Condition\Product\AttributesFactory $attributesFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $attributesFactory, $data);
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
