<?php
namespace Ebizmarts\MailChimp\Controller\Cart\Loadquote;

/**
 * Interceptor class for @see \Ebizmarts\MailChimp\Controller\Cart\Loadquote
 */
class Interceptor extends \Ebizmarts\MailChimp\Controller\Cart\Loadquote implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $pageFactory, \Magento\Quote\Model\QuoteFactory $quote, \Magento\Customer\Model\Session $customerSession, \Ebizmarts\MailChimp\Helper\Data $helper, \Magento\Framework\Url $urlHelper, \Magento\Customer\Model\Url $customerUrl)
    {
        $this->___init();
        parent::__construct($context, $pageFactory, $quote, $customerSession, $helper, $urlHelper, $customerUrl);
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
