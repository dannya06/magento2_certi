<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Api;

interface TaxExtrafeeDetailsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const VALUE_INCL_TAX ='value_incl_tax';
    const VALUE_EXCL_TAX = 'value_excl_tax';
    const ITEMS = 'items';

    /**
     * @return float
     */
    public function getValueInclTax();

    /**
     * @param $amoutInclTax
     * @return TaxExtrafeeDetailsInterface
     */
    public function setValueInclTax($amoutInclTax);

    /**
     * @return float
     */
    public function getValueExclTax();

    /**
     * @param $amountExclTax
     * @return TaxExtrafeeDetailsInterface
     */
    public function setValueExclTax($amountExclTax);

    /**
     * @return array
     */
    public function getItems();

    /**
     * @param string $items
     * @return TaxExtrafeeDetailsInterface
     */
    public function setItems($items);
}
