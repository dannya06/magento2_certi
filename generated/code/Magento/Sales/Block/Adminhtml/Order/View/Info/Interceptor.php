<?php
namespace Magento\Sales\Block\Adminhtml\Order\View\Info;

/**
 * Interceptor class for @see \Magento\Sales\Block\Adminhtml\Order\View\Info
 */
class Interceptor extends \Magento\Sales\Block\Adminhtml\Order\View\Info implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Sales\Helper\Admin $adminHelper, \Magento\Customer\Api\GroupRepositoryInterface $groupRepository, \Magento\Customer\Api\CustomerMetadataInterface $metadata, \Magento\Customer\Model\Metadata\ElementFactory $elementFactory, \Magento\Sales\Model\Order\Address\Renderer $addressRenderer, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $registry, $adminHelper, $groupRepository, $metadata, $elementFactory, $addressRenderer, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toHtml');
        if (!$pluginInfo) {
            return parent::toHtml();
        } else {
            return $this->___callPlugins('toHtml', func_get_args(), $pluginInfo);
        }
    }
}
