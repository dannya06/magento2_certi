<?php
namespace Aheadworks\Followupemail\Controller\Adminhtml\Queue\MassCancel;

/**
 * Interceptor class for @see \Aheadworks\Followupemail\Controller\Adminhtml\Queue\MassCancel
 */
class Interceptor extends \Aheadworks\Followupemail\Controller\Adminhtml\Queue\MassCancel implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context)
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
