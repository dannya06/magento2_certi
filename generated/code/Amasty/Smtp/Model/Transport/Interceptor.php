<?php
namespace Amasty\Smtp\Model\Transport;

/**
 * Interceptor class for @see \Amasty\Smtp\Model\Transport
 */
class Interceptor extends \Amasty\Smtp\Model\Transport implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Mail\MessageInterface $message, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Amasty\Smtp\Model\Logger\MessageLogger $messageLogger, \Amasty\Smtp\Model\Logger\DebugLogger $debugLogger, \Amasty\Smtp\Helper\Data $helper, $host = '127.0.0.1', array $parameters = array())
    {
        $this->___init();
        parent::__construct($message, $scopeConfig, $messageLogger, $debugLogger, $helper, $host, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'sendMessage');
        if (!$pluginInfo) {
            return parent::sendMessage();
        } else {
            return $this->___callPlugins('sendMessage', func_get_args(), $pluginInfo);
        }
    }
}
