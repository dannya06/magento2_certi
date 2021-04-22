<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

/**
 * Class for provide all config
 */
class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * xpath prefix of module (section)
     */
    protected $pathPrefix = 'amasty_extrafee/';

    /**
     * xpath group parts
     */
    const GENERAL_BLOCK = 'general/';
    const CALCULATION_BLOCK = 'calculation/';
    const TAX_BLOCK = 'tax/';
    const FRONTEND_BLOCK = 'frontend/';

    /**
     * xpath field parts
     */
    const CART_FIELD = 'cart';
    const SUBTOTAL_DISCOUNT = 'discount_in_subtotal';
    const SUBTOTAL_TAX = 'tax_in_subtotal';
    const PERCENT_TAX = 'tax_for_percent';
    const SUBTOTAL_SHIPPING = 'shipping_in_subtotal';
    const TAX_CLASS = 'tax_class';
    const CART_PRICES = 'prices_at_cart';
    const SALES_PRICES = 'prices_at_sales';
    const SHOW_ON_ORDER_GRID = 'show_on_order_grid';

    const EXCLUDE_TAX = 0;
    const INCLUDE_TAX = 1;

    /**
     * @return bool
     */
    public function isShowOnCart(): bool
    {
        return $this->isSetFlag(self::FRONTEND_BLOCK . self::CART_FIELD);
    }

    /**
     * @return int
     */
    public function getDiscountInSubtotal(): int
    {
        return (int)$this->getValue(self::CALCULATION_BLOCK . self::SUBTOTAL_DISCOUNT);
    }

    /**
     * @return int
     */
    public function getCalcMethod(): int
    {
        return (int)$this->getValue(self::CALCULATION_BLOCK . self::SUBTOTAL_TAX);
    }

    /**
     * @return bool
     */
    public function useFeeTaxClassForPercentFee(): bool
    {
        return $this->isSetFlag(self::CALCULATION_BLOCK . self::PERCENT_TAX);
    }

    /**
     * @return int
     */
    public function getShippingInSubtotal(): int
    {
        return (int)$this->getValue(self::CALCULATION_BLOCK . self::SUBTOTAL_SHIPPING);
    }

    /**
     * @return string
     */
    public function getTaxClass(): string
    {
        return (string)$this->getValue(self::TAX_BLOCK . self::TAX_CLASS);
    }

    /**
     * @return int
     */
    public function displayCartPrices()
    {
        return (int)$this->getValue(self::TAX_BLOCK . self::CART_PRICES);
    }

    /**
     * @return int
     */
    public function displaySalesPrices()
    {
        return (int)$this->getValue(self::TAX_BLOCK . self::SALES_PRICES);
    }

    /**
     * @return bool
     */
    public function isShowOnOrderGrid(): bool
    {
        return $this->isSetFlag(self::GENERAL_BLOCK . self::SHOW_ON_ORDER_GRID);
    }
}
