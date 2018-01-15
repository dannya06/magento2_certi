<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post\MassStatus;

/**
 * Interceptor class for @see \Aheadworks\Blog\Controller\Adminhtml\Post\MassStatus
 */
class Interceptor extends \Aheadworks\Blog\Controller\Adminhtml\Post\MassStatus implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $collectionFactory, \Aheadworks\Blog\Api\PostRepositoryInterface $postRepository, \Magento\Framework\Stdlib\DateTime\DateTime $dateTime)
    {
        $this->___init();
        parent::__construct($context, $filter, $collectionFactory, $postRepository, $dateTime);
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
