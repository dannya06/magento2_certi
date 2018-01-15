<?php
namespace Aheadworks\Layerednav\Controller\Ajax\ItemsCount;

/**
 * Interceptor class for @see \Aheadworks\Layerednav\Controller\Ajax\ItemsCount
 */
class Interceptor extends \Aheadworks\Layerednav\Controller\Ajax\ItemsCount implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Catalog\Model\Layer\Resolver $layerResolver, \Aheadworks\Layerednav\Model\Layer\FilterListResolver $filterListResolver, \Aheadworks\Layerednav\Model\Applier $applier)
    {
        $this->___init();
        parent::__construct($context, $layerResolver, $filterListResolver, $applier);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
