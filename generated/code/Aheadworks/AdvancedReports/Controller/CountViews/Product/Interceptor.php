<?php
namespace Aheadworks\AdvancedReports\Controller\CountViews\Product;

/**
 * Interceptor class for @see \Aheadworks\AdvancedReports\Controller\CountViews\Product
 */
class Interceptor extends \Aheadworks\AdvancedReports\Controller\CountViews\Product implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Session\SessionManagerInterface $sessionManager, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Model\VisitorFactory $visitorFactory, \Aheadworks\AdvancedReports\Model\ResourceModel\Log\ProductViewFactory $productViewLogResourceFactory, \Aheadworks\AdvancedReports\Model\Log\ProductViewFactory $productViewLogFactory, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository)
    {
        $this->___init();
        parent::__construct($context, $sessionManager, $storeManager, $visitorFactory, $productViewLogResourceFactory, $productViewLogFactory, $customerRepository);
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
