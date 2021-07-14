<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Api;

use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Magento\Framework\Exception\LocalizedException;

interface ExtrafeeQuoteRepositoryInterface
{
    /**
     * Save
     *
     * @param ExtrafeeQuoteInterface $quoteFee
     *
     * @return ExtrafeeQuoteInterface
     */
    public function save(ExtrafeeQuoteInterface $quoteFee);

    /**
     * Get by id
     *
     * @param int $entityId
     *
     * @return ExtrafeeQuoteInterface
     */
    public function getById($entityId);

    /**
     * Delete
     *
     * @param ExtrafeeQuoteInterface $quoteFee
     *
     * @return bool true on success
     */
    public function delete(ExtrafeeQuoteInterface $quoteFee);

    /**
     * Delete by id
     *
     * @param int $entityId
     *
     * @return bool true on success
     */
    public function deleteById($entityId);

    /**
     * @param int $quoteId
     * @param array $requiredFeeIds
     * @throws LocalizedException
     */
    public function checkChosenOptions($quoteId, $requiredFeeIds);
}
