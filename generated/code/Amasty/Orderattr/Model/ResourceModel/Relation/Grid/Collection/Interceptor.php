<?php
namespace Amasty\Orderattr\Model\ResourceModel\Relation\Grid\Collection;

/**
 * Interceptor class for @see \Amasty\Orderattr\Model\ResourceModel\Relation\Grid\Collection
 */
class Interceptor extends \Amasty\Orderattr\Model\ResourceModel\Relation\Grid\Collection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, $mainTable = 'amasty_orderattr_attributes_relation', $resourceModel = '\\Amasty\\Orderattr\\Model\\ResourceModel\\Relation')
    {
        $this->___init();
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
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
