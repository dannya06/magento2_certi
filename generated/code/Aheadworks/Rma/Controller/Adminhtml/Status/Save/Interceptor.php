<?php
namespace Aheadworks\Rma\Controller\Adminhtml\Status\Save;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Adminhtml\Status\Save
 */
class Interceptor extends \Aheadworks\Rma\Controller\Adminhtml\Status\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Rma\Api\StatusRepositoryInterface $statusRepository, \Aheadworks\Rma\Api\Data\StatusInterfaceFactory $statusFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, \Aheadworks\Rma\Model\Status\PostDataProcessor\Status $postDataProcessor)
    {
        $this->___init();
        parent::__construct($context, $statusRepository, $statusFactory, $dataObjectHelper, $dataPersistor, $postDataProcessor);
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
