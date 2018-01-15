<?php
namespace Aheadworks\SocialLogin\Controller\Customer\Accounts;

/**
 * Interceptor class for @see \Aheadworks\SocialLogin\Controller\Customer\Accounts
 */
class Interceptor extends \Aheadworks\SocialLogin\Controller\Customer\Accounts implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Psr\Log\LoggerInterface $logger, \Aheadworks\SocialLogin\Model\Config\General $generalConfig, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Model\Url $customerUrl)
    {
        $this->___init();
        parent::__construct($context, $logger, $generalConfig, $resultPageFactory, $customerSession, $customerUrl);
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
