<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Api\Data;

interface ExtrafeeQuoteInterface
{
    /**
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const QUOTE_ID = 'quote_id';
    const FEE_ID = 'fee_id';
    const OPTION_ID = 'option_id';
    const FEE_AMOUNT = 'fee_amount';
    const BASE_FEE_AMOUNT = 'base_fee_amount';
    const LABEL = 'label';
    const TAX_AMOUNT = 'tax_amount';
    const BASE_TAX_AMOUNT = 'base_tax_amount';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return ExtrafeeQuoteInterface
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @param int $quoteId
     *
     * @return ExtrafeeQuoteInterface
     */
    public function setQuoteId($quoteId);

    /**
     * @return int
     */
    public function getFeeId();

    /**
     * @param int $feeId
     *
     * @return ExtrafeeQuoteInterface
     */
    public function setFeeId($feeId);

    /**
     * @return int
     */
    public function getOptionId();

    /**
     * @param int $optionId
     *
     * @return ExtrafeeQuoteInterface
     */
    public function setOptionId($optionId);

    /**
     * @return float
     */
    public function getFeeAmount();

    /**
     * @param float $feeAmount
     *
     * @return ExtrafeeQuoteInterface
     */
    public function setFeeAmount($feeAmount);

    /**
     * @return float
     */
    public function getBaseFeeAmount();

    /**
     * @param float $feeAmount
     *
     * @return ExtrafeeQuoteInterface
     */
    public function setBaseFeeAmount($feeAmount);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return ExtrafeeQuoteInterface
     */
    public function setLabel($label);

    /**
     * @return float
     */
    public function getTaxAmount();

    /**
     * @param float $taxAmount
     *
     * @return ExtrafeeQuoteInterface
     */
    public function setTaxAmount($taxAmount);

    /**
     * @return float
     */
    public function getBaseTaxAmount();

    /**
     * @param float $taxAmount
     *
     * @return ExtrafeeQuoteInterface
     */
    public function setBaseTaxAmount($taxAmount);
}
