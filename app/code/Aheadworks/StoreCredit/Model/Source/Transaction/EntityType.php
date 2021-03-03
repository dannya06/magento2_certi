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
namespace Aheadworks\StoreCredit\Model\Source\Transaction;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class EntityType
 *
 * @package Aheadworks\StoreCredit\Model\Source
 */
class EntityType implements ArrayInterface
{
    /**#@+
     * Entity type values
     */
    const ORDER_ID = 1;
    const CREDIT_MEMO_ID = 2;
    /**#@-*/

    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ORDER_ID,
                'label' => __('Order Id')
            ],
            [
                'value' => self::CREDIT_MEMO_ID,
                'label' => __('Credit Memo Id')
            ]
        ];
    }

    /**
     * Retrieve entity types
     *
     * @return array
     */
    public function getEntityTypes()
    {
        return [
            self::ORDER_ID,
            self::CREDIT_MEMO_ID
        ];
    }
}
