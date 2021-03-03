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
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Api;

use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\RewardPoints\Model\Source\Transaction\Type as TransactionType;

/**
 * @api
 */
interface TransactionManagementInterface
{
    /**
     * Create empty transaction instance
     *
     * @return TransactionInterface
     */
    public function createEmptyTransaction();

    /**
     * Create transaction
     *
     * @param CustomerInterface $customer
     * @param int $balance
     * @param string $expirationDate
     * @param string $commentToCustomer
     * @param string $commentToCustomerPlaceholder
     * @param string $commentToAdmin
     * @param string $commentToAdminPlaceholder
     * @param int $websiteId
     * @param int $transactionType
     * @param array $arguments
     * @return boolean
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function createTransaction(
        CustomerInterface $customer,
        $balance,
        $expirationDate = null,
        $commentToCustomer = null,
        $commentToCustomerPlaceholder = null,
        $commentToAdmin = null,
        $commentToAdminPlaceholder = null,
        $websiteId = null,
        $transactionType = TransactionType::BALANCE_ADJUSTED_BY_ADMIN,
        $arguments = []
    );

    /**
     * Save transaction
     *
     * @param TransactionInterface $transaction
     * @param array $arguments
     * @return boolean
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveTransaction(TransactionInterface $transaction, $arguments = []);
}
