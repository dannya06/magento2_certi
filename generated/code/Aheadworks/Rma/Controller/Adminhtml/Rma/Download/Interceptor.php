<?php
namespace Aheadworks\Rma\Controller\Adminhtml\Rma\Download;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Adminhtml\Rma\Download
 */
class Interceptor extends \Aheadworks\Rma\Controller\Adminhtml\Rma\Download implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Rma\Api\ThreadMessageManagementInterface $threadMessageManagement, \Aheadworks\Rma\Model\ThreadMessage\Attachment\FileDownloader $fileDownloader)
    {
        $this->___init();
        parent::__construct($context, $threadMessageManagement, $fileDownloader);
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
