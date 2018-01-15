<?php
namespace Aheadworks\SocialLogin\Controller\Account\Unlink;

/**
 * Interceptor class for @see \Aheadworks\SocialLogin\Controller\Account\Unlink
 */
class Interceptor extends \Aheadworks\SocialLogin\Controller\Account\Unlink implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Psr\Log\LoggerInterface $logger, \Aheadworks\SocialLogin\Model\Config\General $generalConfig, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Aheadworks\SocialLogin\Api\AccountRepositoryInterface $accountRepository, \Magento\Customer\Model\Session $customerSession)
    {
        $this->___init();
        parent::__construct($context, $logger, $generalConfig, $formKeyValidator, $accountRepository, $customerSession);
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
