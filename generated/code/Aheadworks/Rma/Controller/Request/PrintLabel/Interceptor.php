<?php
namespace Aheadworks\Rma\Controller\Request\PrintLabel;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Request\PrintLabel
 */
class Interceptor extends \Aheadworks\Rma\Controller\Request\PrintLabel implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Aheadworks\Rma\Model\Request\Resolver\Status $statusResolver, \Aheadworks\Rma\Api\RequestRepositoryInterface $requestRepository, \Aheadworks\Rma\Model\Request\PrintLabel\Pdf $printLabelPdf, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Aheadworks\Rma\Model\Url\ParamEncryptor $encryptor)
    {
        $this->___init();
        parent::__construct($context, $statusResolver, $requestRepository, $printLabelPdf, $fileFactory, $encryptor);
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
