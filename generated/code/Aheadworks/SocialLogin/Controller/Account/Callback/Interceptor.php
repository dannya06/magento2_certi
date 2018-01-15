<?php
namespace Aheadworks\SocialLogin\Controller\Account\Callback;

/**
 * Interceptor class for @see \Aheadworks\SocialLogin\Controller\Account\Callback
 */
class Interceptor extends \Aheadworks\SocialLogin\Controller\Account\Callback implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Psr\Log\LoggerInterface $logger, \Aheadworks\SocialLogin\Model\Config\General $generalConfig, \Aheadworks\SocialLogin\Model\ProviderManagement $providerManagement, \Aheadworks\SocialLogin\Helper\State $stateHelper, array $forwardsMap = array())
    {
        $this->___init();
        parent::__construct($context, $logger, $generalConfig, $providerManagement, $stateHelper, $forwardsMap);
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
