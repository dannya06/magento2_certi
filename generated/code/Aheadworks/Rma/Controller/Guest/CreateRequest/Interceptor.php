<?php
namespace Aheadworks\Rma\Controller\Guest\CreateRequest;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Guest\CreateRequest
 */
class Interceptor extends \Aheadworks\Rma\Controller\Guest\CreateRequest implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Aheadworks\Rma\Model\Config $config, \Magento\Sales\Api\OrderRepositoryInterface $orderRepository, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Aheadworks\Rma\Model\Request\Order $requestOrder, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $config, $orderRepository, $searchCriteriaBuilder, $requestOrder, $formKeyValidator);
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
