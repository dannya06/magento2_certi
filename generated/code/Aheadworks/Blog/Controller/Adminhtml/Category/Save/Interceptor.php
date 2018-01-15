<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Category\Save;

/**
 * Interceptor class for @see \Aheadworks\Blog\Controller\Adminhtml\Category\Save
 */
class Interceptor extends \Aheadworks\Blog\Controller\Adminhtml\Category\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Blog\Api\CategoryRepositoryInterface $categoryRepository, \Aheadworks\Blog\Api\Data\CategoryInterfaceFactory $categoryDataFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor)
    {
        $this->___init();
        parent::__construct($context, $categoryRepository, $categoryDataFactory, $dataObjectHelper, $storeManager, $dataPersistor);
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
