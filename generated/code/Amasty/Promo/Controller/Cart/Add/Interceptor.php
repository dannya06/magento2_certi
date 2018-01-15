<?php
namespace Amasty\Promo\Controller\Cart\Add;

/**
 * Interceptor class for @see \Amasty\Promo\Controller\Cart\Add
 */
class Interceptor extends \Amasty\Promo\Controller\Cart\Add implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Amasty\Promo\Model\Registry $promoRegistry, \Amasty\Promo\Helper\Cart $promoCartHelper)
    {
        $this->___init();
        parent::__construct($context, $promoRegistry, $promoCartHelper);
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
