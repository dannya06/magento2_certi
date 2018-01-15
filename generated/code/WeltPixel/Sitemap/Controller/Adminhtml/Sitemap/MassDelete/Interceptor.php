<?php
namespace WeltPixel\Sitemap\Controller\Adminhtml\Sitemap\MassDelete;

/**
 * Interceptor class for @see \WeltPixel\Sitemap\Controller\Adminhtml\Sitemap\MassDelete
 */
class Interceptor extends \WeltPixel\Sitemap\Controller\Adminhtml\Sitemap\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \WeltPixel\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $filter, $collectionFactory);
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
