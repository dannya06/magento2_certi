<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Api;

interface FeeRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Extrafee\Api\Data\FeeInterface $fee
     * @param string[] $options
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function save(\Amasty\Extrafee\Api\Data\FeeInterface $fee, $options);

    /**
     * Get by id
     *
     * @param int $feeId
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function getById($feeId);

    /**
     * Delete
     *
     * @param \Amasty\Extrafee\Api\Data\FeeInterface $fee
     * @return bool true on success
     */
    public function delete(\Amasty\Extrafee\Api\Data\FeeInterface $fee);

    /**
     * Delete by id
     *
     * @param int $feeId
     * @return bool true on success
     */
    public function deleteById($feeId);

    /**
     * Lists by quote
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Amasty\Extrafee\Api\Data\FeeInterface[]
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Quote\Api\Data\CartInterface $quote
    );

    /**
     * Lists
     *
     * @return \Amasty\Extrafee\Api\Data\FeeInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getListItems();

    /**
     * @param $optionId
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function getByOptionId($optionId);
}
