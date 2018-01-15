<?php
namespace Aheadworks\SocialLogin\Controller\Account\Callback\Login;

/**
 * Interceptor class for @see \Aheadworks\SocialLogin\Controller\Account\Callback\Login
 */
class Interceptor extends \Aheadworks\SocialLogin\Controller\Account\Callback\Login implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Psr\Log\LoggerInterface $logger, \Aheadworks\SocialLogin\Model\Config\General $generalConfig, \Aheadworks\SocialLogin\Model\ProviderManagement $providerManagement, \Aheadworks\SocialLogin\Helper\State $stateHelper, \Aheadworks\SocialLogin\Api\AccountRepositoryInterface $accountRepository, \Aheadworks\SocialLogin\Model\Customer\AccountManagement $accountManagement, \Magento\Customer\Model\Account\Redirect $accountRedirect, \Magento\Customer\Model\Session $customerSession)
    {
        $this->___init();
        parent::__construct($context, $logger, $generalConfig, $providerManagement, $stateHelper, $accountRepository, $accountManagement, $accountRedirect, $customerSession);
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
