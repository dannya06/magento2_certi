<?php
namespace Xtento\OrderExport\Controller\Adminhtml\Tools\ExportSettings;

/**
 * Interceptor class for @see \Xtento\OrderExport\Controller\Adminhtml\Tools\ExportSettings
 */
class Interceptor extends \Xtento\OrderExport\Controller\Adminhtml\Tools\ExportSettings implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Xtento\OrderExport\Helper\Module $moduleHelper, \Xtento\XtCore\Helper\Cron $cronHelper, \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Xtento\OrderExport\Model\ProfileFactory $profileFactory, \Xtento\OrderExport\Model\DestinationFactory $destinationFactory, \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData, \Xtento\XtCore\Helper\Utils $utilsHelper, \Xtento\OrderExport\Helper\Tools $toolsHelper)
    {
        $this->___init();
        parent::__construct($context, $moduleHelper, $cronHelper, $profileCollectionFactory, $scopeConfig, $profileFactory, $destinationFactory, $requestData, $utilsHelper, $toolsHelper);
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
