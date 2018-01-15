<?php
namespace Magento\Tax\Block\Item\Price\Renderer;

/**
 * Interceptor class for @see \Magento\Tax\Block\Item\Price\Renderer
 */
class Interceptor extends \Magento\Tax\Block\Item\Price\Renderer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Tax\Helper\Data $taxHelper, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $taxHelper, $priceCurrency, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalAmount($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTotalAmount');
        if (!$pluginInfo) {
            return parent::getTotalAmount($item);
        } else {
            return $this->___callPlugins('getTotalAmount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseTotalAmount($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBaseTotalAmount');
        if (!$pluginInfo) {
            return parent::getBaseTotalAmount($item);
        } else {
            return $this->___callPlugins('getBaseTotalAmount', func_get_args(), $pluginInfo);
        }
    }
}
