<?php
namespace Icube\Snap\Controller\Payment\Fail;

/**
 * Interceptor class for @see \Icube\Snap\Controller\Payment\Fail
 */
class Interceptor extends \Icube\Snap\Controller\Payment\Fail implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Catalog\Model\Product $product, \Magento\Checkout\Model\Cart $cart, \Magento\Framework\App\ResponseFactory $responseFactory, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\App\Response\Http $response, \Magento\Framework\Module\Manager $moduleManager, \Magento\Store\Model\StoreManagerInterface $storeManagerInterface)
    {
        $this->___init();
        parent::__construct($context, $product, $cart, $responseFactory, $checkoutSession, $response, $moduleManager, $storeManagerInterface);
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
