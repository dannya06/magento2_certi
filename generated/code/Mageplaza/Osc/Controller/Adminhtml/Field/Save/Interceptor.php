<?php
namespace Mageplaza\Osc\Controller\Adminhtml\Field\Save;

/**
 * Interceptor class for @see \Mageplaza\Osc\Controller\Adminhtml\Field\Save
 */
class Interceptor extends \Mageplaza\Osc\Controller\Adminhtml\Field\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Config\Model\ResourceModel\Config $resourceConfig, \Magento\Framework\App\Config\ReinitableConfigInterface $config)
    {
        $this->___init();
        parent::__construct($context, $resourceConfig, $config);
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
