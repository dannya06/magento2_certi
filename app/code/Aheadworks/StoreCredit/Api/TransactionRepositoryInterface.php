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
namespace Aheadworks\StoreCredit\Api;

use Aheadworks\StoreCredit\Api\Data\TransactionInterface;
use Aheadworks\StoreCredit\Api\Data\TransactionSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface TransactionRepositoryInterface
{
    /**
     * Retrieve transaction data by id
     *
     * @param  int $id
     * @return TransactionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Create transaction instance
     *
     * @return TransactionInterface
     */
    public function create();

    /**
     * Save transaction data
     *
     * @param TransactionInterface $transaction
     * @param array $arguments
     * @return TransactionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(TransactionInterface $transaction, $arguments = []);

    /**
     * Retrieve transaction matching the specified criteria
     *
     * @param  SearchCriteriaInterface $criteria
     * @return TransactionSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria);
}
