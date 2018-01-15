<?php
namespace Aheadworks\Rma\Controller\Adminhtml\Rma\MassChangeStatus;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Adminhtml\Rma\MassChangeStatus
 */
class Interceptor extends \Aheadworks\Rma\Controller\Adminhtml\Rma\MassChangeStatus implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Rma\Model\ResourceModel\Request\CollectionFactory $collectionFactory, \Magento\Ui\Component\MassAction\Filter $filter, \Aheadworks\Rma\Api\RequestManagementInterface $requestManagement)
    {
        $this->___init();
        parent::__construct($context, $collectionFactory, $filter, $requestManagement);
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
