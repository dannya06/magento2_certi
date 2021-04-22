<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote;

use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ExtrafeeQuoteInterface::ENTITY_ID;

    protected function _construct()
    {
        $this->_init(\Amasty\Extrafee\Model\ExtrafeeQuote::class, ExtrafeeQuote::class);
    }

    /**
     * @param int $quoteId
     * @return array
     */
    public function getFeeByQuoteId($quoteId)
    {
        $this->addFilterByQuoteId($quoteId)
            ->addExpressionFieldToSelect('fee_amount', 'SUM({{fee_amount}})', 'fee_amount')
            ->addExpressionFieldToSelect('base_fee_amount', 'SUM({{base_fee_amount}})', 'base_fee_amount')
            ->addExpressionFieldToSelect('tax_amount', 'SUM({{tax_amount}})', 'tax_amount')
            ->addExpressionFieldToSelect('base_tax_amount', 'SUM({{base_tax_amount}})', 'base_tax_amount')
            ->addExpressionFieldToSelect('fee_id', 'GROUP_CONCAT(DISTINCT({{fee_id}}))', 'fee_id');

        return $this->getConnection()->fetchRow($this->getSelect());
    }

    /**
     * @param int $quoteId
     * @return Collection
     */
    public function addFilterByQuoteId($quoteId)
    {
        return $this->addFieldToFilter('quote_id', $quoteId);
    }

    /**
     * @param int $feeId
     * @param int $quoteId
     * @return Collection
     */
    public function addFilterByFeeAndQuote($feeId, $quoteId)
    {
        return $this->addFieldToFilter('fee_id', $feeId)
            ->addFilterByQuoteId($quoteId);
    }

    /**
     * @param int $feeId
     * @param int $quoteId
     * @param array $optionIds
     * @return Collection
     */
    public function addFilterByFeeQuoteOptions($feeId, $quoteId, $optionIds)
    {
        return $this->addFilterByFeeAndQuote($feeId, $quoteId)
            ->addFieldToFilter('option_id', ['in' => $optionIds]);
    }
}
