<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Category\MassDelete;

/**
 * Interceptor class for @see \Aheadworks\Blog\Controller\Adminhtml\Category\MassDelete
 */
class Interceptor extends \Aheadworks\Blog\Controller\Adminhtml\Category\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $collectionFactory, \Aheadworks\Blog\Api\CategoryRepositoryInterface $categoryRepository)
    {
        $this->___init();
        parent::__construct($context, $filter, $collectionFactory, $categoryRepository);
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
