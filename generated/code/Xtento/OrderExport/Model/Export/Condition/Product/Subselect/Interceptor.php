<?php
namespace Xtento\OrderExport\Model\Export\Condition\Product\Subselect;

/**
 * Interceptor class for @see \Xtento\OrderExport\Model\Export\Condition\Product\Subselect
 */
class Interceptor extends \Xtento\OrderExport\Model\Export\Condition\Product\Subselect implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Rule\Model\Condition\Context $context, \Magento\Framework\Event\ManagerInterface $eventManager, \Xtento\OrderExport\Model\Export\Condition\CustomFactory $conditionCustomFactory, \Magento\Framework\Registry $registry, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $eventManager, $conditionCustomFactory, $registry, $data);
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
