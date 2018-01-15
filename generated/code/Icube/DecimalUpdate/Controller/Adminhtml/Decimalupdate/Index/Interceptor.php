<?php
namespace Icube\DecimalUpdate\Controller\Adminhtml\Decimalupdate\Index;

/**
 * Interceptor class for @see \Icube\DecimalUpdate\Controller\Adminhtml\Decimalupdate\Index
 */
class Interceptor extends \Icube\DecimalUpdate\Controller\Adminhtml\Decimalupdate\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($context, $logger);
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
