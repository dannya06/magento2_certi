<?php
namespace Xtento\OrderExport\Controller\Adminhtml\Manual\GridPost;

/**
 * Interceptor class for @see \Xtento\OrderExport\Controller\Adminhtml\Manual\GridPost
 */
class Interceptor extends \Xtento\OrderExport\Controller\Adminhtml\Manual\GridPost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Xtento\OrderExport\Helper\Module $moduleHelper, \Xtento\XtCore\Helper\Cron $cronHelper, \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Xtento\OrderExport\Model\ProfileFactory $profileFactory, \Magento\Framework\Registry $registry, \Xtento\OrderExport\Model\ExportFactory $exportFactory, \Xtento\XtCore\Helper\Utils $utilsHelper, \Xtento\OrderExport\Helper\Entity $entityHelper)
    {
        $this->___init();
        parent::__construct($context, $filter, $moduleHelper, $cronHelper, $profileCollectionFactory, $scopeConfig, $profileFactory, $registry, $exportFactory, $utilsHelper, $entityHelper);
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
