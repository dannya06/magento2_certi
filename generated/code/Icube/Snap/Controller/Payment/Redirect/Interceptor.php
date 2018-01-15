<?php
namespace Icube\Snap\Controller\Payment\Redirect;

/**
 * Interceptor class for @see \Icube\Snap\Controller\Payment\Redirect
 */
class Interceptor extends \Icube\Snap\Controller\Payment\Redirect implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($moduleManager, $context);
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
