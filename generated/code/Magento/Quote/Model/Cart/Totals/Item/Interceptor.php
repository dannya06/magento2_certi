<?php
namespace Magento\Quote\Model\Cart\Totals\Item;

/**
 * Interceptor class for @see \Magento\Quote\Model\Cart\Totals\Item
 */
class Interceptor extends \Magento\Quote\Model\Cart\Totals\Item implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, \Magento\Framework\Api\AttributeValueFactory $attributeValueFactory, $data = array())
    {
        $this->___init();
        parent::__construct($extensionFactory, $attributeValueFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCustomAttribute');
        if (!$pluginInfo) {
            return parent::setCustomAttribute($attributeCode, $attributeValue);
        } else {
            return $this->___callPlugins('setCustomAttribute', func_get_args(), $pluginInfo);
        }
    }
}
