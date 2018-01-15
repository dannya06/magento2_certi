<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post\Delete;

/**
 * Interceptor class for @see \Aheadworks\Blog\Controller\Adminhtml\Post\Delete
 */
class Interceptor extends \Aheadworks\Blog\Controller\Adminhtml\Post\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Blog\Api\PostRepositoryInterface $postRepository, \Aheadworks\Blog\Api\Data\PostInterfaceFactory $postDataFactory, \Aheadworks\Blog\Model\PostFactory $postFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Stdlib\DateTime\DateTime $dateTime, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, \Aheadworks\Blog\Model\Converter\Condition $conditionConverter, \Aheadworks\Blog\Model\Rule\ProductFactory $productRuleFactory)
    {
        $this->___init();
        parent::__construct($context, $postRepository, $postDataFactory, $postFactory, $dataObjectHelper, $dataObjectProcessor, $resultPageFactory, $resultForwardFactory, $resultJsonFactory, $dateTime, $storeManager, $dataPersistor, $conditionConverter, $productRuleFactory);
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
