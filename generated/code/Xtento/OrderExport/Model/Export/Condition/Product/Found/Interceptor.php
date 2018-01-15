<?php
namespace Xtento\OrderExport\Model\Export\Condition\Product\Found;

/**
 * Interceptor class for @see \Xtento\OrderExport\Model\Export\Condition\Product\Found
 */
class Interceptor extends \Xtento\OrderExport\Model\Export\Condition\Product\Found implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Rule\Model\Condition\Context $context, \Magento\SalesRule\Model\Rule\Condition\Product $ruleConditionProduct, \Xtento\OrderExport\Model\Export\Condition\Product $conditionProduct, \Xtento\OrderExport\Model\Export\Condition\Custom $conditionCustom, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $ruleConditionProduct, $conditionProduct, $conditionCustom, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        if (!$pluginInfo) {
            return parent::validate($model);
        } else {
            return $this->___callPlugins('validate', func_get_args(), $pluginInfo);
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
