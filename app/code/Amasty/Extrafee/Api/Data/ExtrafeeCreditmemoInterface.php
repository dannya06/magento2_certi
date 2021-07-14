<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Api\Data;

interface ExtrafeeCreditmemoInterface
{
    /**
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const ORDER_ID = 'order_id';
    const CREDITMEMO_ID = 'creditmemo_id';
    const FEE_ID = 'fee_id';
    const OPTION_ID = 'option_id';
    const BASE_TOTAL = 'base_total_amount';
    const TOTAL = 'total_amount';
    const BASE_TAX = 'base_tax_amount';
    const TAX = 'tax_amount';
    const LABEL = 'fee_label';
    const OPTION_LABEL = 'fee_option_label';

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @param int $entityId
     *
     * @return void
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getOrderId(): int;

    /**
     * @param int $orderId
     *
     * @return void
     */
    public function setOrderId(int $orderId);

    /**
     * @return int
     */
    public function getCreditmemoId(): int;

    /**
     * @param int $creditmemoId
     *
     * @return void
     */
    public function setCreditmemoId(int $creditmemoId);

    /**
     * @return int
     */
    public function getFeeId(): int;

    /**
     * @param int $feeId
     *
     * @return void
     */
    public function setFeeId(int $feeId);

    /**
     * @return int
     */
    public function getOptionId(): int;

    /**
     * @param int $optionId
     *
     * @return void
     */
    public function setOptionId(int $optionId);

    /**
     * @return float
     */
    public function getBaseTotalAmount(): float;

    /**
     * @param float $total
     *
     * @return void
     */
    public function setBaseTotalAmount($total);

    /**
     * @return float
     */
    public function getTotalAmount(): float;

    /**
     * @param float $total
     *
     * @return void
     */
    public function setTotalAmount($total);

    /**
     * @return float
     */
    public function getBaseTaxAmount(): float;

    /**
     * @param float $tax
     *
     * @return void
     */
    public function setBaseTaxAmount($tax);

    /**
     * @return float
     */
    public function getTaxAmount(): float;

    /**
     * @param float $tax
     *
     * @return void
     */
    public function setTaxAmount($tax);

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @param string $label
     *
     * @return void
     */
    public function setFeeLabel($label);

    /**
     * @return string
     */
    public function getOptionLabel(): string;

    /**
     * @param string $label
     *
     * @return void
     */
    public function setFeeOptionLabel($label);
}
