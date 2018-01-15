<?php
namespace Aheadworks\Giftcard\Controller\Product\Preview;

/**
 * Interceptor class for @see \Aheadworks\Giftcard\Controller\Product\Preview
 */
class Interceptor extends \Aheadworks\Giftcard\Controller\Product\Preview implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Aheadworks\Giftcard\Model\Email\Previewer $previewer)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $previewer);
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
