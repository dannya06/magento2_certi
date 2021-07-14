<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Api\Data;

interface ExtrafeeOrderInterface
{
    /**
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const ORDER_ID = 'order_id';
    const FEE_ID = 'fee_id';
    const OPTION_ID = 'option_id';
    const BASE_TOTAL = 'base_total_amount';
    const BASE_TOTAL_INVOICED = 'base_total_amount_invoiced';
    const BASE_TOTAL_REFUNDED = 'base_total_amount_refunded';
    const TOTAL = 'total_amount';
    const TOTAL_INVOICED = 'total_amount_invoiced';
    const TOTAL_REFUNDED = 'total_amount_refunded';
    const BASE_TAX = 'base_tax_amount';
    const BASE_TAX_INVOICED = 'base_tax_amount_invoiced';
    const BASE_TAX_REFUNDED = 'base_tax_amount_refunded';
    const TAX = 'tax_amount';
    const TAX_INVOICED = 'tax_amount_invoiced';
    const TAX_REFUNDED = 'tax_amount_refunded';
    const LABEL = 'fee_label';
    const OPTION_LABEL = 'fee_option_label';
    const IS_REFUNDED = 'is_refunded';

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
    public function getBaseTotalAmountInvoiced(): float;

    /**
     * @param float $total
     *
     * @return void
     */
    public function setBaseTotalAmountInvoiced($total);

    /**
     * @return float
     */
    public function getBaseTotalAmountRefunded(): float;

    /**
     * @param float $total
     *
     * @return void
     */
    public function setBaseTotalAmountRefunded($total);

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
    public function getTotalAmountInvoiced(): float;

    /**
     * @param float $total
     *
     * @return void
     */
    public function setTotalAmountInvoiced($total);

    /**
     * @return float
     */
    public function getTotalAmountRefunded(): float;

    /**
     * @param float $total
     *
     * @return void
     */
    public function setTotalAmountRefunded($total);

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
    public function getBaseTaxAmountInvoiced(): float;

    /**
     * @param float $tax
     *
     * @return void
     */
    public function setBaseTaxAmountInvoiced($tax);

    /**
     * @return float
     */
    public function getBaseTaxAmountRefunded(): float;

    /**
     * @param float $tax
     *
     * @return void
     */
    public function setBaseTaxAmountRefunded($tax);

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
     * @return float
     */
    public function getTaxAmountInvoiced(): float;

    /**
     * @param float $tax
     *
     * @return void
     */
    public function setTaxAmountInvoiced($tax);

    /**
     * @return float
     */
    public function getTaxAmountRefunded(): float;

    /**
     * @param float $tax
     *
     * @return void
     */
    public function setTaxAmountRefunded($tax);

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @param string $label
     *
     * @return void
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getOptionLabel(): string;

    /**
     * @param string $label
     *
     * @return void
     */
    public function setOptionLabel($label);

    /**
     * @return bool
     */
    public function isRefunded(): bool;

    /**
     * @param bool $isRefunded
     *
     * @return void
     */
    public function setIsRefunded(bool $isRefunded);
}
