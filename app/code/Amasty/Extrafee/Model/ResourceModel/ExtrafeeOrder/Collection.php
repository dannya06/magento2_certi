<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder;
use Amasty\Extrafee\Model\ResourceModel\Fee;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ExtrafeeOrderInterface::ENTITY_ID;

    protected function _construct()
    {
        $this->_init(\Amasty\Extrafee\Model\ExtrafeeOrder::class, ExtrafeeOrder::class);
    }

    /**
     * @param int $orderId
     * @return Collection
     */
    public function addFilterByOrderId($orderId)
    {
        return $this->addFieldToFilter(ExtrafeeOrderInterface::ORDER_ID, $orderId);
    }

    /**
     * @param array $eligibleIds
     * @return Collection
     */
    public function addFilterByFeeIds($eligibleIds)
    {
        return $this->addFieldToFilter(ExtrafeeOrderInterface::FEE_ID, ['in' => $eligibleIds]);
    }

    /**
     * @param int $orderId
     * @return Collection
     */
    public function getFeeOrderCollectionByOrderId($orderId)
    {
        return $this->addFilterByOrderId($orderId)
            ->joinFees()
            ->addFieldToFilter(FeeInterface::IS_ELIGIBLE_REFUND, 1);
    }

    /**
     * @return $this
     */
    public function joinFees()
    {
        $this->getSelect()
            ->join(
                ['amfees' => $this->getTable(Fee::TABLE_NAME)],
                'amfees.entity_id = main_table.fee_id',
                [FeeInterface::IS_ELIGIBLE_REFUND]
            );

        return $this;
    }
}
