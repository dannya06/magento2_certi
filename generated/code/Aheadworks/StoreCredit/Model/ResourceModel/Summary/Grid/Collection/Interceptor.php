<?php
namespace Aheadworks\StoreCredit\Model\ResourceModel\Summary\Grid\Collection;

/**
 * Interceptor class for @see \Aheadworks\StoreCredit\Model\ResourceModel\Summary\Grid\Collection
 */
class Interceptor extends \Aheadworks\StoreCredit\Model\ResourceModel\Summary\Grid\Collection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, \Aheadworks\StoreCredit\Model\Config $config, \Magento\Store\Model\StoreManagerInterface $storeManager, $mainTable, $resourceModel)
    {
        $this->___init();
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $config, $storeManager, $mainTable, $resourceModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelect()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSelect');
        if (!$pluginInfo) {
            return parent::getSelect();
        } else {
            return $this->___callPlugins('getSelect', func_get_args(), $pluginInfo);
        }
    }
}
