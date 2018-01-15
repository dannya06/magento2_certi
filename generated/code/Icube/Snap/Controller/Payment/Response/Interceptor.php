<?php
namespace Icube\Snap\Controller\Payment\Response;

/**
 * Interceptor class for @see \Icube\Snap\Controller\Payment\Response
 */
class Interceptor extends \Icube\Snap\Controller\Payment\Response implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Catalog\Model\Product $product, \Magento\Checkout\Model\Cart $cart, \Magento\Framework\App\ResponseFactory $responseFactory)
    {
        $this->___init();
        parent::__construct($context, $product, $cart, $responseFactory);
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
