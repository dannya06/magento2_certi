<?php
namespace Aheadworks\Giftcard\Controller\Card\CheckCode;

/**
 * Interceptor class for @see \Aheadworks\Giftcard\Controller\Card\CheckCode
 */
class Interceptor extends \Aheadworks\Giftcard\Controller\Card\CheckCode implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Aheadworks\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Escaper $escaper)
    {
        $this->___init();
        parent::__construct($context, $giftcardRepository, $storeManager, $escaper);
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
