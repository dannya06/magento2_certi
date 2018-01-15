<?php
namespace Aheadworks\Followupemail\Controller\Adminhtml\Event\SendTest;

/**
 * Interceptor class for @see \Aheadworks\Followupemail\Controller\Adminhtml\Event\SendTest
 */
class Interceptor extends \Aheadworks\Followupemail\Controller\Adminhtml\Event\SendTest implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Followupemail\Model\EventFactory $eventModelFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Aheadworks\Followupemail\Model\Sender $sender)
    {
        $this->___init();
        parent::__construct($context, $eventModelFactory, $resultJsonFactory, $sender);
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
