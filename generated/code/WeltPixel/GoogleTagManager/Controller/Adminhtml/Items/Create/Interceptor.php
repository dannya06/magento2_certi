<?php
namespace WeltPixel\GoogleTagManager\Controller\Adminhtml\Items\Create;

/**
 * Interceptor class for @see \WeltPixel\GoogleTagManager\Controller\Adminhtml\Items\Create
 */
class Interceptor extends \WeltPixel\GoogleTagManager\Controller\Adminhtml\Items\Create implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\WeltPixel\GoogleTagManager\Model\Api $apiModel, \Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory)
    {
        $this->___init();
        parent::__construct($apiModel, $context, $resultJsonFactory);
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
