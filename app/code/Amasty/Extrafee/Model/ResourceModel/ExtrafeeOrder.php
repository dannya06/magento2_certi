<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\ResourceModel;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ExtrafeeOrder extends AbstractDb
{
    const TABLE_NAME = 'amasty_extrafee_order';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ExtrafeeOrderInterface::ENTITY_ID);
    }

    /**
     * @param array $ordersIds
     * @return array
     */
    public function getLabelsForOrders(array $ordersIds): array
    {
        $labelColumn = ExtrafeeOrderInterface::OPTION_LABEL;

        $select = $this->getConnection()->select()
            ->from(['t' => $this->getMainTable()], [
                'order_id' => ExtrafeeOrderInterface::ORDER_ID,
                'label' => new \Zend_Db_Expr('GROUP_CONCAT(`' . $labelColumn . '` SEPARATOR ", ")')
            ])
            ->where(ExtrafeeOrderInterface::ORDER_ID . ' IN(?)', $ordersIds)
            ->group(ExtrafeeOrderInterface::ORDER_ID);

        return $this->getConnection()->fetchPairs($select);
    }
}
