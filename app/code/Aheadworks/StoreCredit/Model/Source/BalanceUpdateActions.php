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
 * Class BalanceUpdateActions
 *
 * @package Aheadworks\StoreCredit\Model\Source
 */
class BalanceUpdateActions implements ArrayInterface
{
    /**
     * @var TransactionType
     */
    private $transactionType;

    /**
     * @param TransactionType $transactionType
     */
    public function __construct(TransactionType $transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     *  {@inheritDoc}
     */
    public function toOptionArray()
    {
        return $this->transactionType->getBalanceUpdateActions();
    }
}
