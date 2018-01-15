<?php
namespace Amasty\Smtp\Controller\Adminhtml\Config\Check;

/**
 * Interceptor class for @see \Amasty\Smtp\Controller\Adminhtml\Config\Check
 */
class Interceptor extends \Amasty\Smtp\Controller\Adminhtml\Config\Check implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, $instanceName = 'Amasty\\Smtp\\Model\\Transport')
    {
        $this->___init();
        parent::__construct($context, $instanceName);
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
