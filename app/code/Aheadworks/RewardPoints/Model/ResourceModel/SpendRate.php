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
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Model\ResourceModel;

use Aheadworks\RewardPoints\Model\SpendRate as SpendRateModel;
use Magento\Customer\Api\Data\GroupInterface;

/**
 * Class Aheadworks\RewardPoints\Model\ResourceModel\SpendRate
 */
class SpendRate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init('aw_rp_spend_rate', 'rate_id');
    }

    /**
     * Save configuration values
     *
     * @param  array $configValue
     * @throws \Exception
     * @return \Aheadworks\RewardPoints\Model\ResourceModel\SpendRate
     */
    public function saveConfigValue($configValue)
    {
        if (is_array($configValue)) {
            $connection = $this->transactionManager->start($this->getConnection());
            try {
                $this->clear();
                foreach ($configValue as $configData) {
                    if (is_array($configData)) {
                        $bind = $this->prepareConfigData($configData);
                        $connection->insert($this->getMainTable(), $bind);
                    }
                }
                $this->transactionManager->commit();
            } catch (\Exception $e) {
                $this->transactionManager->rollBack();
                throw $e;
            }
        }

        return $this;
    }

    /**
     * Prepare data for save configuration
     *
     * @param  array $configData
     * @return array
     */
    protected function prepareConfigData($configData)
    {
        $data = [];
        $connection = $this->getConnection();
        $fields = $connection->describeTable($this->getMainTable());
        foreach (array_keys($fields) as $field) {
            if (isset($configData[$field])) {
                $fieldValue = $configData[$field];
                if ($fieldValue instanceof \Zend_Db_Expr) {
                    $data[$field] = $fieldValue;
                } else {
                    if (null !== $fieldValue) {
                        $fieldValue = $this->_prepareTableValueForSave($fieldValue, $fields[$field]['DATA_TYPE']);
                        $data[$field] = $connection->prepareColumnValue($fields[$field], $fieldValue);
                    }
                }
            } elseif (! empty($fields[$field]['NULLABLE'])) {
                $data[$field] = null;
            }
        }
        return $data;
    }

    /**
     * Clear data
     *
     * @throws \Exception
     * @return \Aheadworks\RewardPoints\Model\ResourceModel\SpendRate
     */
    public function clear()
    {
        $connection = $this->transactionManager->start($this->getConnection());
        try {
            $connection->delete($this->getMainTable());
            $this->transactionManager->commit();
        } catch (\Exception $e) {
            $this->transactionManager->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Get rate row id by customer group id,
     * lifetime sales amount and website id
     *
     * @param int $customerGroupId
     * @param int $lifetimeSalesAmount
     * @param int $websiteId
     * @param bool $min
     * @return string|NULL
     */
    public function getRateRowId($customerGroupId, $lifetimeSalesAmount, $websiteId, $min = false)
    {
        $select = $this->getRateRowSelect($customerGroupId, $lifetimeSalesAmount, $websiteId, 'rate_id', $min);
        if ($select instanceof \Magento\Framework\DB\Select) {
            return $this->getConnection()->fetchOne($select);
        }
        return null;
    }

    /**
     * Get select query by customer group id,
     * lifetime sales amount and website id
     *
     * @param int $customerGroupId
     * @param int $lifetimeSalesAmount
     * @param int $websiteId
     * @param string $cols
     * @param bool $min
     * @return \Magento\Framework\DB\Select
     */
    private function getRateRowSelect(
        $customerGroupId,
        $lifetimeSalesAmount,
        $websiteId,
        $cols = '*',
        $min = false
    ) {
        $connection = $this->getConnection();

        if ($connection && $customerGroupId !== null && $websiteId !== null) {
            $mainTable = $this->getMainTable();
            $select = $connection->select()->from($mainTable, $cols);

            $customerGroupIdField = $connection->quoteIdentifier(
                sprintf('%s.%s', $mainTable, SpendRateModel::CUSTOMER_GROUP_ID)
            );
            $websiteIdField = $connection->quoteIdentifier(
                sprintf('%s.%s', $mainTable, SpendRateModel::WEBSITE_ID)
            );
            $lifetimeSalesAmountField = $connection->quoteIdentifier(
                sprintf('%s.%s', $mainTable, SpendRateModel::LIFETIME_SALES_AMOUNT)
            );

            $select->where(
                '(' . $customerGroupIdField.'=? OR ' .
                $customerGroupIdField . '=' . GroupInterface::CUST_GROUP_ALL . ')',
                $customerGroupId
            );

            $select->where($websiteIdField . '=?', $websiteId);
            if (null !== $lifetimeSalesAmount) {
                $select->where($lifetimeSalesAmountField . '<=?', $lifetimeSalesAmount);
            }
            $select->order(SpendRateModel::LIFETIME_SALES_AMOUNT . ($min ? ' ASC' : ' DESC'));
            $select->order(SpendRateModel::CUSTOMER_GROUP_ID . ' ASC');

            return $select;
        }
        return null;
    }
}
