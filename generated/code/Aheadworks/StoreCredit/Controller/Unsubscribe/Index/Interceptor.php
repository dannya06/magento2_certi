<?php
namespace Aheadworks\StoreCredit\Controller\Unsubscribe\Index;

/**
 * Interceptor class for @see \Aheadworks\StoreCredit\Controller\Unsubscribe\Index
 */
class Interceptor extends \Aheadworks\StoreCredit\Controller\Unsubscribe\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Aheadworks\StoreCredit\Model\Service\SummaryService $summaryService, \Magento\Framework\DataObject $dataObject, \Aheadworks\StoreCredit\Model\KeyEncryptor $keyEncryptor)
    {
        $this->___init();
        parent::__construct($context, $summaryService, $dataObject, $keyEncryptor);
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
