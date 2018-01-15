<?php
namespace Aheadworks\Followupemail\Controller\Adminhtml\Event\Edit;

/**
 * Interceptor class for @see \Aheadworks\Followupemail\Controller\Adminhtml\Event\Edit
 */
class Interceptor extends \Aheadworks\Followupemail\Controller\Adminhtml\Event\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry, \Aheadworks\Followupemail\Model\EventFactory $eventModelFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $registry, $eventModelFactory);
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
