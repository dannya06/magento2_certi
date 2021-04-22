<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\ResourceModel;

use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ExtrafeeQuote extends AbstractDb
{
    const TABLE_NAME = 'amasty_extrafee_quote';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ExtrafeeQuoteInterface::ENTITY_ID);
    }

    /**
     * @param int $quoteId
     * @param array $requiredFeeIds
     * @return array
     * @throws LocalizedException
     */
    public function getChosenOptions($quoteId, $requiredFeeIds)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable($this->getMainTable()), ['fee_id', 'COUNT(*)'])
            ->where('fee_id IN(?)', $requiredFeeIds)
            ->where('quote_id =?', $quoteId)
            ->where('option_id != 0')
            ->group(['fee_id', 'quote_id']);

        return $this->getConnection()->fetchPairs($select);
    }
}
