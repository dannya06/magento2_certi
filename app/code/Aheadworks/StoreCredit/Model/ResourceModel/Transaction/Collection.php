<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    StoreCredit
 * @version    1.1.7
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\StoreCredit\Model\ResourceModel\Transaction;

use Aheadworks\StoreCredit\Api\Data\TransactionSearchResultsInterface;
use Aheadworks\StoreCredit\Model\Transaction;
use Aheadworks\StoreCredit\Model\ResourceModel\Transaction as TransactionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Aheadworks\StoreCredit\Model\ResourceModel\Transaction\Collection
 */
class Collection extends AbstractCollection implements TransactionSearchResultsInterface
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(Transaction::class, TransactionResource::class);
    }

    /**
     * Add customer filter
     *
     * @param int|string $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        $this->addFieldToFilter('customer_id', ['eq' => $customerId]);
        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        $this->searchCriteria = $searchCriteria;
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        if (!$items) {
            return $this;
        }
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'created_by') {
            return $this->addCreatedByFilter($condition);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add created by filter
     *
     * @param string $createdBy
     * @return $this
     */
    public function addCreatedByFilter($createdBy)
    {
        $this->addFilter('created_by', $createdBy, 'public');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            'admin_user',
            'created_by',
            'user_id',
            '',
            'created_by'
        );
        $this->attachRelationTable(
            'aw_sc_transaction_entity',
            'transaction_id',
            'transaction_id',
            '',
            'entities'
        );
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable('admin_user', 'created_by', 'user_id', 'created_by');
        parent::_renderFiltersBefore();
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnFilter
     * @return void
     */
    private function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnFilter
    ) {
        if ($this->getFilter($columnFilter)) {
            $linkageTableName = $columnFilter . '_table';
            $select = $this->getSelect();
            $select->joinLeft(
                [$linkageTableName => $this->getTable($tableName)],
                'main_table.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );
            switch ($columnFilter) {
                case 'created_by':
                    $this->addFilterToMap(
                        $columnFilter,
                        'CONCAT_WS(" ", ' . $linkageTableName . '.firstname, ' . $linkageTableName . '.lastname)'
                    );
                    break;
                default:
                    $this->addFilterToMap($columnFilter, $columnFilter . '_table.' . $columnFilter);
                    break;
            }
        }
    }

    /**
     * Attach entity table data to collection items
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnNameRelationTable
     * @param string $fieldName
     * @return void
     */
    private function attachRelationTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable,
        $fieldName
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from([$tableName . '_table' => $this->getTable($tableName)])
                ->where($tableName . '_table.' . $linkageColumnName . ' IN (?)', $ids);

            $relationTableData = $connection->fetchAll($select);

            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $result = [];
                $id = $item->getData($columnName);

                foreach ($relationTableData as $dataRow) {
                    $result = $this->processDataRow(
                        $dataRow,
                        $linkageColumnName,
                        $id,
                        $columnNameRelationTable,
                        $fieldName,
                        $result
                    );
                }
                $item->setData($fieldName, $result);
            }
        }
    }

    /**
     * Process data row to attach
     *
     * @param array $dataRow
     * @param string $linkageColumnName
     * @param int $id
     * @param string $columnNameRelationTable
     * @param string $fieldName
     * @param string|array $result
     * @return string|array
     */
    private function processDataRow(
        $dataRow,
        $linkageColumnName,
        $id,
        $columnNameRelationTable,
        $fieldName,
        $result
    ) {
        if ($dataRow[$linkageColumnName] == $id) {
            switch ($fieldName) {
                case 'created_by':
                    $result = $dataRow['firstname'] . ' ' . $dataRow['lastname'];
                    break;
                case 'entities':
                    $result[$dataRow['entity_type']] = [
                        'entity_id'    => $dataRow['entity_id'],
                        'entity_label' => $dataRow['entity_label']
                    ];
                    break;
                default:
                    $result[] = $dataRow[$columnNameRelationTable];
                    break;
            }
        }
        return $result;
    }
}
