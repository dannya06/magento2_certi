<?php
namespace Icube\Confirmpayment\Controller\Index\Save;

/**
 * Interceptor class for @see \Icube\Confirmpayment\Controller\Index\Save
 */
class Interceptor extends \Icube\Confirmpayment\Controller\Index\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory, \Magento\Framework\Filesystem $fileSystem, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->___init();
        parent::__construct($context, $fileUploaderFactory, $fileSystem, $transportBuilder, $scopeConfig);
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
