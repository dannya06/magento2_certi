<?php
namespace Aheadworks\AdvancedReports\Controller\Adminhtml\Statistics\Schedule;

/**
 * Interceptor class for @see \Aheadworks\AdvancedReports\Controller\Adminhtml\Statistics\Schedule
 */
class Interceptor extends \Aheadworks\AdvancedReports\Controller\Adminhtml\Statistics\Schedule implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\AdvancedReports\Model\Indexer\Statistics\Processor $statisticsProcessor)
    {
        $this->___init();
        parent::__construct($context, $statisticsProcessor);
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
