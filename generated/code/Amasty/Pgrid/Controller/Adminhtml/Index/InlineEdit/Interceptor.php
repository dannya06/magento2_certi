<?php
namespace Amasty\Pgrid\Controller\Adminhtml\Index\InlineEdit;

/**
 * Interceptor class for @see \Amasty\Pgrid\Controller\Adminhtml\Index\InlineEdit
 */
class Interceptor extends \Amasty\Pgrid\Controller\Adminhtml\Index\InlineEdit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Amasty\Pgrid\Ui\Component\Listing\Attribute\Repository $attributeRepository, \Psr\Log\LoggerInterface $logger, \Magento\Framework\View\Element\UiComponentFactory $factory, \Amasty\Pgrid\Helper\Data $helper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $productRepository, $attributeRepository, $logger, $factory, $helper, $storeManager, $stockRegistry);
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
