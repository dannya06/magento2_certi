<?php
namespace Aheadworks\Rma\Controller\Guest\Index;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Guest\Index
 */
class Interceptor extends \Aheadworks\Rma\Controller\Guest\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Aheadworks\Rma\Model\Config $config)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $config);
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
