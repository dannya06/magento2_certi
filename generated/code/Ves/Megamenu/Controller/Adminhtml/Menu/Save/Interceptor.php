<?php
namespace Ves\Megamenu\Controller\Adminhtml\Menu\Save;

/**
 * Interceptor class for @see \Ves\Megamenu\Controller\Adminhtml\Menu\Save
 */
class Interceptor extends \Ves\Megamenu\Controller\Adminhtml\Menu\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\App\ResourceConnection $resource, \Magento\Store\Model\StoreManagerInterface $storeManager, \Ves\Megamenu\Helper\Generator $generatorhelper)
    {
        $this->___init();
        parent::__construct($context, $resource, $storeManager, $generatorhelper);
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
