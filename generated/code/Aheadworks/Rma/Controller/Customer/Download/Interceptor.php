<?php
namespace Aheadworks\Rma\Controller\Customer\Download;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Customer\Download
 */
class Interceptor extends \Aheadworks\Rma\Controller\Customer\Download implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Aheadworks\Rma\Api\RequestRepositoryInterface $requestRepository, \Magento\Customer\Model\Session $customerSession, \Aheadworks\Rma\Api\ThreadMessageManagementInterface $threadMessageManagement, \Aheadworks\Rma\Model\ThreadMessage\Attachment\FileDownloader $fileDownloader)
    {
        $this->___init();
        parent::__construct($context, $requestRepository, $customerSession, $threadMessageManagement, $fileDownloader);
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
