<?php
namespace Aheadworks\Rma\Controller\Adminhtml\Rma\Edit;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Adminhtml\Rma\Edit
 */
class Interceptor extends \Aheadworks\Rma\Controller\Adminhtml\Rma\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Rma\Api\RequestRepositoryInterface $requestRepository, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->___init();
        parent::__construct($context, $requestRepository, $resultPageFactory);
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
