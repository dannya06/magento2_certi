<?php
namespace Aheadworks\Giftcard\Controller\Cart\Remove;

/**
 * Interceptor class for @see \Aheadworks\Giftcard\Controller\Cart\Remove
 */
class Interceptor extends \Aheadworks\Giftcard\Controller\Cart\Remove implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Aheadworks\Giftcard\Api\GiftcardCartManagementInterface $giftcardCartManagement, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\Escaper $escaper)
    {
        $this->___init();
        parent::__construct($context, $giftcardCartManagement, $checkoutSession, $escaper);
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
