<?php
namespace Aheadworks\Rma\Controller\Adminhtml\Status\Preview;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Adminhtml\Status\Preview
 */
class Interceptor extends \Aheadworks\Rma\Controller\Adminhtml\Status\Preview implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Framework\Data\Form\FormKey $formKey, \Aheadworks\Rma\Api\Data\StatusInterfaceFactory $statusFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Aheadworks\Rma\Model\Status\PostDataProcessor\PreviewEmail $previewEmailPostDataProcessor, \Aheadworks\Rma\Model\StorefrontValueResolver $storefrontValueResolver)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $layoutFactory, $formKey, $statusFactory, $dataObjectHelper, $previewEmailPostDataProcessor, $storefrontValueResolver);
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
