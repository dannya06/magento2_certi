<?php
namespace Aheadworks\Layerednav\Block\Navigation\FilterRenderer;

/**
 * Interceptor class for @see \Aheadworks\Layerednav\Block\Navigation\FilterRenderer
 */
class Interceptor extends \Aheadworks\Layerednav\Block\Navigation\FilterRenderer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Catalog\Model\Layer\Resolver $layerResolver, \Aheadworks\Layerednav\Model\Config $config, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $layerResolver, $config, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Catalog\Model\Layer\Filter\FilterInterface $filter)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'render');
        if (!$pluginInfo) {
            return parent::render($filter);
        } else {
            return $this->___callPlugins('render', func_get_args(), $pluginInfo);
        }
    }
}
