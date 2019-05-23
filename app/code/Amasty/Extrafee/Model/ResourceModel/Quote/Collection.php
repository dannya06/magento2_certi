<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\ResourceModel\Quote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const FEE_DATA = [];

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(\Amasty\Extrafee\Model\Quote::class, \Amasty\Extrafee\Model\ResourceModel\Quote::class);
    }

    /**
     * @param $quoteId
     * @return array
     */
    public function getFeeByQuoteId($quoteId)
    {
        $this->addFieldToFilter('quote_id', $quoteId)
            ->addExpressionFieldToSelect('fee_amount', 'SUM({{fee_amount}})', 'fee_amount')
            ->addExpressionFieldToSelect('base_fee_amount', 'SUM({{base_fee_amount}})', 'base_fee_amount')
            ->addExpressionFieldToSelect('tax_amount', 'SUM({{tax_amount}})', 'tax_amount')
            ->addExpressionFieldToSelect('base_tax_amount', 'SUM({{base_tax_amount}})', 'base_tax_amount')
            ->addExpressionFieldToSelect('fee_id', 'GROUP_CONCAT(DISTINCT({{fee_id}}))', 'fee_id');

        return $this->getConnection()->fetchRow($this->getSelect());
    }
}
