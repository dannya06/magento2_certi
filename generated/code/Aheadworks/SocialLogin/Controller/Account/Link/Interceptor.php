<?php
namespace Aheadworks\SocialLogin\Controller\Account\Link;

/**
 * Interceptor class for @see \Aheadworks\SocialLogin\Controller\Account\Link
 */
class Interceptor extends \Aheadworks\SocialLogin\Controller\Account\Link implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Psr\Log\LoggerInterface $logger, \Aheadworks\SocialLogin\Model\Config\General $generalConfig, \Aheadworks\SocialLogin\Model\ProviderManagement $providerManagement, \Aheadworks\SocialLogin\Helper\State $stateHelper)
    {
        $this->___init();
        parent::__construct($context, $logger, $generalConfig, $providerManagement, $stateHelper);
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
