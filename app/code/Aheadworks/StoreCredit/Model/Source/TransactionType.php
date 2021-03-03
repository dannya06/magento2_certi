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
namespace Aheadworks\StoreCredit\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TransactionType
 *
 * @package Aheadworks\StoreCredit\Model\Source
 */
class TransactionType implements ArrayInterface
{
    /**#@+
     * Balance update action values
     */
    const BALANCE_ADJUSTED_BY_ADMIN = 1;
    const ORDER_CANCELED = 2;
    const REFUND_BY_STORE_CREDIT = 3;
    const REIMBURSE_OF_SPENT_STORE_CREDIT = 4;
    const STORE_CREDIT_USED_IN_ORDER = 5;
    /**#@-*/

    /**
     *  {@inheritDoc}
     */
    public function toOptionArray()
    {
        return $this->getBalanceUpdateActions();
    }

    /**
     *  {@inheritDoc}
     */
    public function getBalanceUpdateActions()
    {
        return [
            [
                'value' => self::BALANCE_ADJUSTED_BY_ADMIN,
                'label' => __('Balance adjusted by admin')
            ],
            [
                'value' => self::ORDER_CANCELED,
                'label' => __('Order canceled')
            ],
            [
                'value' => self::REFUND_BY_STORE_CREDIT,
                'label' => __('Refund by Store Credit')
            ],
            [
                'value' => self::REIMBURSE_OF_SPENT_STORE_CREDIT,
                'label' => __('Reimburse of spent Store Credit')
            ],
            [
                'value' => self::STORE_CREDIT_USED_IN_ORDER,
                'label' => __('Store Credit used in order')
            ]
        ];
    }
}
