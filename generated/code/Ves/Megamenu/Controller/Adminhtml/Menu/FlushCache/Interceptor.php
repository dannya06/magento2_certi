<?php
namespace Ves\Megamenu\Controller\Adminhtml\Menu\FlushCache;

/**
 * Interceptor class for @see \Ves\Megamenu\Controller\Adminhtml\Menu\FlushCache
 */
class Interceptor extends \Ves\Megamenu\Controller\Adminhtml\Menu\FlushCache implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\App\ResourceConnection $resource)
    {
        $this->___init();
        parent::__construct($context, $resource);
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
