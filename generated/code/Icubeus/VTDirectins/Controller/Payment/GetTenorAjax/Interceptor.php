<?php
namespace Icubeus\VTDirectins\Controller\Payment\GetTenorAjax;

/**
 * Interceptor class for @see \Icubeus\VTDirectins\Controller\Payment\GetTenorAjax
 */
class Interceptor extends \Icubeus\VTDirectins\Controller\Payment\GetTenorAjax implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($context);
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
