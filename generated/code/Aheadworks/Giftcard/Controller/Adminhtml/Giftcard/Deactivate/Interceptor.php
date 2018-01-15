<?php
namespace Aheadworks\Giftcard\Controller\Adminhtml\Giftcard\Deactivate;

/**
 * Interceptor class for @see \Aheadworks\Giftcard\Controller\Adminhtml\Giftcard\Deactivate
 */
class Interceptor extends \Aheadworks\Giftcard\Controller\Adminhtml\Giftcard\Deactivate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->___init();
        parent::__construct($context, $giftcardRepository, $resultPageFactory);
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
