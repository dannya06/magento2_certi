<?php
namespace Aheadworks\Rma\Controller\Guest\PrintLabel;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Guest\PrintLabel
 */
class Interceptor extends \Aheadworks\Rma\Controller\Guest\PrintLabel implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Aheadworks\Rma\Api\RequestRepositoryInterface $requestRepository, \Aheadworks\Rma\Model\Config $config, \Aheadworks\Rma\Api\RequestManagementInterface $requestManagement)
    {
        $this->___init();
        parent::__construct($context, $requestRepository, $config, $requestManagement);
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
