<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post\WordpressImport;

/**
 * Interceptor class for @see \Aheadworks\Blog\Controller\Adminhtml\Post\WordpressImport
 */
class Interceptor extends \Aheadworks\Blog\Controller\Adminhtml\Post\WordpressImport implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Aheadworks\Blog\Model\WordpressImport $wpImporter, \Magento\Backend\App\Action\Context $context, \Aheadworks\Blog\Model\FileUploader $fileUploader)
    {
        $this->___init();
        parent::__construct($wpImporter, $context, $fileUploader);
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
