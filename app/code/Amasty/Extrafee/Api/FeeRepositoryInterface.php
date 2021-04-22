<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Api;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Api\Data\FeeSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface FeeRepositoryInterface
{
    /**
     * Save
     *
     * @param FeeInterface $fee
     * @param string[] $options
     * @return FeeInterface
     */
    public function save(FeeInterface $fee, $options);

    /**
     * Get by id
     *
     * @param int $feeId
     * @return FeeInterface
     */
    public function getById($feeId);

    /**
     * Delete
     *
     * @param FeeInterface $fee
     * @return bool true on success
     */
    public function delete(FeeInterface $fee);

    /**
     * Delete by id
     *
     * @param int $feeId
     * @return bool true on success
     */
    public function deleteById($feeId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return FeeSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param int $optionId
     * @return FeeInterface
     */
    public function getByOptionId($optionId);

    /**
     * @return FeeInterface
     */
    public function create();
}
