<?php
namespace Xtento\OrderExport\Controller\Adminhtml\Profile\DownloadTestExport;

/**
 * Interceptor class for @see \Xtento\OrderExport\Controller\Adminhtml\Profile\DownloadTestExport
 */
class Interceptor extends \Xtento\OrderExport\Controller\Adminhtml\Profile\DownloadTestExport implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Xtento\OrderExport\Helper\Module $moduleHelper, \Xtento\XtCore\Helper\Cron $cronHelper, \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\Registry $registry, \Magento\Framework\Escaper $escaper, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter, \Xtento\OrderExport\Helper\Entity $entityHelper, \Xtento\XtCore\Helper\Utils $utilsHelper, \Xtento\OrderExport\Model\ProfileFactory $profileFactory)
    {
        $this->___init();
        parent::__construct($context, $moduleHelper, $cronHelper, $profileCollectionFactory, $filesystem, $registry, $escaper, $scopeConfig, $dateFilter, $entityHelper, $utilsHelper, $profileFactory);
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
