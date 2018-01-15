<?php
namespace Aheadworks\Rma\Controller\Adminhtml\CustomField\Save;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Adminhtml\CustomField\Save
 */
class Interceptor extends \Aheadworks\Rma\Controller\Adminhtml\CustomField\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Rma\Api\CustomFieldRepositoryInterface $customFieldRepository, \Aheadworks\Rma\Api\Data\CustomFieldInterfaceFactory $customFieldFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, \Aheadworks\Rma\Controller\Adminhtml\CustomField\PostDataProcessor $postDataProcessor)
    {
        $this->___init();
        parent::__construct($context, $customFieldRepository, $customFieldFactory, $dataObjectHelper, $dataPersistor, $postDataProcessor);
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
