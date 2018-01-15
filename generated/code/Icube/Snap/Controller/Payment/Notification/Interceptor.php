<?php
namespace Icube\Snap\Controller\Payment\Notification;

/**
 * Interceptor class for @see \Icube\Snap\Controller\Payment\Notification
 */
class Interceptor extends \Icube\Snap\Controller\Payment\Notification implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Registry $registry, \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $orderCommentSender)
    {
        $this->___init();
        parent::__construct($context, $registry, $orderCommentSender);
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
