<?php
namespace Aheadworks\Rma\Controller\Adminhtml\Status\Edit;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Adminhtml\Status\Edit
 */
class Interceptor extends \Aheadworks\Rma\Controller\Adminhtml\Status\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Rma\Api\StatusRepositoryInterface $statusRepository, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->___init();
        parent::__construct($context, $statusRepository, $resultPageFactory);
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
