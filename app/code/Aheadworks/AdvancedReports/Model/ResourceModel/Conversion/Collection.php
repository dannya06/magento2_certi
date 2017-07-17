<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Conversion;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\Conversion as ResourceConversion;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Conversion
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var bool
     */
    protected $periodBased = true;

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceConversion::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->columns($this->getColumns(false));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $ordersCount = 'SUM(main_table.orders_count)';
        $viewsCount = 'SUM(main_table.views_count)';
        return [
            'period' => 'period',
            'views_count' => 'COALESCE(SUM(main_table.views_count), 0)',
            'orders_count' => 'COALESCE(SUM(main_table.orders_count), 0)',
            'conversion_rate' => 'IF(' . $viewsCount . ' > 0, ' .
                'IF(' . $viewsCount . ' < ' . $ordersCount . ', 100, ' .
                $ordersCount . ' / ' . $viewsCount . ' * 100), 0)',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'periodFilter') {
            return $this->addGroupByFilter();
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrderStatuses()
    {
        $orderStatuses = parent::getOrderStatuses();
        $orderStatuses[] = ResourceConversion::VIEWED_STATUS;
        return $orderStatuses;
    }
}
