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
namespace Aheadworks\StoreCredit\Model\ResourceModel;

use Aheadworks\StoreCredit\Model\Summary as SummaryModel;

/**
 * Class Aheadworks\StoreCredit\Model\ResourceModel\Summary
 */
class Summary extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *  {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init('aw_sc_summary', 'summary_id');
    }

    /**
     * Load Store Credit summary by customer id
     *
     * @param  SummaryModel $summary
     * @param  int $customerId
     * @return \Aheadworks\StoreCredit\Model\ResourceModel\Summary
     */
    public function loadByCustomerId(SummaryModel $summary, $customerId)
    {
        return $this->load($summary, $customerId, SummaryModel::CUSTOMER_ID);
    }

    /**
     * Get id by customer id
     *
     * @param int $customerId
     * @return int
     */
    public function getIdByCustomerId($customerId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getMainTable(), 'summary_id')
            ->where('customer_id = :customer_id');

        $bind = [':customer_id' => (int)$customerId];

        return $connection->fetchOne($select, $bind);
    }
}
