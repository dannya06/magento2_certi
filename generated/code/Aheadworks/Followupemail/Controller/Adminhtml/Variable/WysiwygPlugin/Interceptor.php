<?php
namespace Aheadworks\Followupemail\Controller\Adminhtml\Variable\WysiwygPlugin;

/**
 * Interceptor class for @see \Aheadworks\Followupemail\Controller\Adminhtml\Variable\WysiwygPlugin
 */
class Interceptor extends \Aheadworks\Followupemail\Controller\Adminhtml\Variable\WysiwygPlugin implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Variable\Model\Variable $customVariable, \Magento\Email\Model\Source\Variables $contactVariables, \Aheadworks\Followupemail\Model\Source\VariablesFactory $followupVariablesFactory)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $customVariable, $contactVariables, $followupVariablesFactory);
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
