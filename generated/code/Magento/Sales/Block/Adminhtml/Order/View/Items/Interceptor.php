<?php
namespace Magento\Sales\Block\Adminhtml\Order\View\Items;

/**
 * Interceptor class for @see \Magento\Sales\Block\Adminhtml\Order\View\Items
 */
class Interceptor extends \Magento\Sales\Block\Adminhtml\Order\View\Items implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration, \Magento\Framework\Registry $registry, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getColumns');
        if (!$pluginInfo) {
            return parent::getColumns();
        } else {
            return $this->___callPlugins('getColumns', func_get_args(), $pluginInfo);
        }
    }
}
