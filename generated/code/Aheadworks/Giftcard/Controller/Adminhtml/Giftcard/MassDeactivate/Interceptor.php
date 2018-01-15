<?php
namespace Aheadworks\Giftcard\Controller\Adminhtml\Giftcard\MassDeactivate;

/**
 * Interceptor class for @see \Aheadworks\Giftcard\Controller\Adminhtml\Giftcard\MassDeactivate
 */
class Interceptor extends \Aheadworks\Giftcard\Controller\Adminhtml\Giftcard\MassDeactivate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Aheadworks\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $collectionFactory, \Aheadworks\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository)
    {
        $this->___init();
        parent::__construct($context, $filter, $collectionFactory, $giftcardRepository);
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
