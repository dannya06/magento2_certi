<?php
namespace Aheadworks\Rma\Controller\Adminhtml\Rma\Save;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Adminhtml\Rma\Save
 */
class Interceptor extends \Aheadworks\Rma\Controller\Adminhtml\Rma\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Aheadworks\Rma\Api\RequestManagementInterface $requestManagement, \Aheadworks\Rma\Model\Request\PostDataProcessor\Composite $requestPostDataProcessor, \Aheadworks\Rma\Api\Data\RequestInterfaceFactory $requestFactory, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $dataObjectHelper, $requestManagement, $requestPostDataProcessor, $requestFactory, $dataPersistor);
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
