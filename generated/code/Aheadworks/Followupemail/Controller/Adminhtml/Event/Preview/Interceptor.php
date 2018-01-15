<?php
namespace Aheadworks\Followupemail\Controller\Adminhtml\Event\Preview;

/**
 * Interceptor class for @see \Aheadworks\Followupemail\Controller\Adminhtml\Event\Preview
 */
class Interceptor extends \Aheadworks\Followupemail\Controller\Adminhtml\Event\Preview implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Followupemail\Model\EventFactory $eventModelFactory, \Magento\Framework\Registry $registry, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Data\Form\FormKey $formKey)
    {
        $this->___init();
        parent::__construct($context, $eventModelFactory, $registry, $resultJsonFactory, $formKey);
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
