<?php
namespace Aheadworks\AdvancedReports\Controller\Adminhtml\CustomerSales\Customers\Index;

/**
 * Interceptor class for @see \Aheadworks\AdvancedReports\Controller\Adminhtml\CustomerSales\Customers\Index
 */
class Interceptor extends \Aheadworks\AdvancedReports\Controller\Adminhtml\CustomerSales\Customers\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Locale\FormatInterface $localeFormat, \Aheadworks\AdvancedReports\Model\Filter\Store $storeFilter, \Aheadworks\AdvancedReports\Model\Filter\Range $rangeFilter)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $localeFormat, $storeFilter, $rangeFilter);
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
