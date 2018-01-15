<?php
namespace Aheadworks\RewardPoints\Model\ResourceModel\PointsSummary\Grid\Collection;

/**
 * Interceptor class for @see \Aheadworks\RewardPoints\Model\ResourceModel\PointsSummary\Grid\Collection
 */
class Interceptor extends \Aheadworks\RewardPoints\Model\ResourceModel\PointsSummary\Grid\Collection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, \Aheadworks\RewardPoints\Model\Config $config, \Magento\Store\Model\StoreManagerInterface $storeManager, $mainTable, $resourceModel)
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
