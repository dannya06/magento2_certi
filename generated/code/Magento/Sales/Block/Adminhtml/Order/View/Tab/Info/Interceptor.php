<?php
namespace Magento\Sales\Block\Adminhtml\Order\View\Tab\Info;

/**
 * Interceptor class for @see \Magento\Sales\Block\Adminhtml\Order\View\Tab\Info
 */
class Interceptor extends \Magento\Sales\Block\Adminhtml\Order\View\Tab\Info implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Sales\Helper\Admin $adminHelper, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftOptionsHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getGiftOptionsHtml');
        if (!$pluginInfo) {
            return parent::getGiftOptionsHtml();
        } else {
            return $this->___callPlugins('getGiftOptionsHtml', func_get_args(), $pluginInfo);
        }
    }
}
