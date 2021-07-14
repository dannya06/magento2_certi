<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\ResourceModel\Fee;

use Amasty\Extrafee\Model\ResourceModel\Fee;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\Extrafee\Api\Data\FeeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /** @var MetadataPool  */
    protected $_metadataPool;

    /** @var StoreManagerInterface  */
    protected $_storeManager;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        MetadataPool $metadataPool,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_metadataPool = $metadataPool;
        return parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    protected function _construct()
    {
        $this->_init(\Amasty\Extrafee\Model\Fee::class, Fee::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->_metadataPool->getMetadata(FeeInterface::class);
        $this->performAfterLoad(
            Fee::STORE_TABLE_NAME,
            'amasty_extrafee_entity_store',
            $entityMetadata->getLinkField(),
            'fee_id',
            'store_id'
        );
        $this->performAfterLoad(
            Fee::GROUP_TABLE_NAME,
            'amasty_extrafee_entity_customer_group',
            $entityMetadata->getLinkField(),
            'fee_id',
            'customer_group_id'
        );
        return parent::_afterLoad();
    }

    /**
     * @param $tableName
     * @param $alias
     * @param $linkField
     * @param $fkField
     * @param $targetField
     */
    protected function performAfterLoad($tableName, $alias, $linkField, $fkField, $targetField)
    {
        $linkedIds = $this->getColumnValues($linkField);

        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from([
                $alias => $this->getTable($tableName)
            ])->where($alias . '.' . $fkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $data = [];
                foreach ($result as $item) {
                    $data[$item[$fkField]][] = $item[$targetField];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($data[$linkedId])) {
                        continue;
                    }
                    $item->setData($targetField, $data[$linkedId]);
                }
            }
        }
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this|Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        } elseif ($field === 'customer_group_id') {
            return $this->addFilter('customer_group_id', $condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store_id', ['in' => $store], 'public');

        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinStoreRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('store_id')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.entity_id = store_table.' . $linkField,
                []
            )->group(
                'main_table.entity_id'
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinGroupRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('customer_group_id')) {
            $this->getSelect()->join(
                ['group_table' => $this->getTable($tableName)],
                'main_table.entity_id = group_table.' . $linkField,
                []
            )->group(
                'main_table.entity_id'
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable(Fee::STORE_TABLE_NAME, 'fee_id');
        $this->joinGroupRelationTable(Fee::GROUP_TABLE_NAME, 'fee_id');
    }
}
