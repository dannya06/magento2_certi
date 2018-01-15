<?php
namespace Icubeus\VTDirect\Controller\Payment\Getpromo;

/**
 * Interceptor class for @see \Icubeus\VTDirect\Controller\Payment\Getpromo
 */
class Interceptor extends \Icubeus\VTDirect\Controller\Payment\Getpromo implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Stdlib\DateTime\DateTime $coreDate)
    {
        $this->___init();
        parent::__construct($context, $coreDate);
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
