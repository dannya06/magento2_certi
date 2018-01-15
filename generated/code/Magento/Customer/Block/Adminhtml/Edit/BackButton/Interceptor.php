<?php
namespace Magento\Customer\Block\Adminhtml\Edit\BackButton;

/**
 * Interceptor class for @see \Magento\Customer\Block\Adminhtml\Edit\BackButton
 */
class Interceptor extends \Magento\Customer\Block\Adminhtml\Edit\BackButton implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    public function getBackUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBackUrl');
        if (!$pluginInfo) {
            return parent::getBackUrl();
        } else {
            return $this->___callPlugins('getBackUrl', func_get_args(), $pluginInfo);
        }
    }
}
