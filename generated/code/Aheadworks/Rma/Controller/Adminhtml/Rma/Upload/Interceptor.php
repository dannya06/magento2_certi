<?php
namespace Aheadworks\Rma\Controller\Adminhtml\Rma\Upload;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Adminhtml\Rma\Upload
 */
class Interceptor extends \Aheadworks\Rma\Controller\Adminhtml\Rma\Upload implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Rma\Model\ThreadMessage\Attachment\FileUploader $fileUploader, \Aheadworks\Rma\Model\Config $config)
    {
        $this->___init();
        parent::__construct($context, $fileUploader, $config);
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
