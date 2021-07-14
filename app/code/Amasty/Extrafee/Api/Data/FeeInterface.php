<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Api\Data;

use Amasty\Extrafee\Model\Config\Source\Excludeinclude;

interface FeeInterface
{
    const ENTITY_ID = 'entity_id';
    const ENABLED = 'enabled';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const OPTIONS = 'options';
    const BASE_OPTIONS = 'base_options';
    const CURRENT_VALUE = 'current_value';
    const FRONTEND_TYPE = 'frontend_type';
    const DISCOUNT_IN_SUBTOTAL = 'discount_in_subtotal';
    const TAX_IN_SUBTOTAL = 'tax_in_subtotal';
    const SHIPPING_IN_SUBTOTAL = 'shipping_in_subtotal';
    const SORT_ORDER = 'sort_order';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const STORE_ID = 'store_id';
    const IS_REQUIRED = 'is_required';
    const IS_ELIGIBLE_REFUND = 'is_eligible_refund';
    const IS_PER_PRODUCT = 'is_per_product';
    const PRODUCT_CONDITIONS_SERIALIZED = 'product_conditions_serialized';

    /**
     * Get ID
     * @return int|null
     */
    public function getId();

    /**
     * Get enabled
     * @return bool
     */
    public function getEnabled();

    /**
     * Get name
     * @return string
     */
    public function getName();

    /**
     * Get description
     * @return string
     */
    public function getDescription();

    /**
     * Get fees options
     * @return array Format: array(array(
     *  'entity_id' => 0,
     *  'fee_id' => 0,
     *  'price' => 0,
     *  'order' => 0,
     *  'price_type' => 'fixed',
     *  'default' => 0,
     *  'admin' => '',
     *  'options' => array()
     * ))
     */
    public function getOptions();

    /**
     * Get current value
     * @return string
     */
    public function getCurrentValue();

    /**
     * Get fees base options
     * @return string
     */
    public function getBaseOptions();

    /**
     * Get $frontendType
     * @return string
     */
    public function getFrontendType();

    /**
     * @return mixed
     */
    public function getDiscountInSubtotal();

    /**
     * @return mixed
     */
    public function getShippingInSubtotal();

    /**
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @return string[]
     */
    public function getGroupId();

    /**
     * @return string[]
     */
    public function getStoreId();

    /**
     * @return bool
     */
    public function isRequired();

    /**
     * @return bool
     */
    public function isPerProduct(): bool;

    /**
     * @return null|string
     */
    public function getProductConditionsSerialized(): ?string;

    /**
     * @param bool $enabled
     * @return FeeInterface
     */
    public function setEnabled($enabled);

    /**
     * @param string $name
     * @return FeeInterface
     */
    public function setName($name);

    /**
     * @param string $description
     * @return FeeInterface
     */
    public function setDescription($description);

    /**
     * @param string[] $options
     * @return FeeInterface
     */
    public function setOptions($options);

    /**
     * @param mixed $currentValue
     * @return FeeInterface
     */
    public function setCurrentValue($currentValue);

    /**
     * @param string $frontendType
     * @return FeeInterface
     */
    public function setFrontendType($frontendType);

    /**
     * @param mixed $discountInSubtotal
     * @return FeeInterface
     */
    public function setDiscountInSubtotal($discountInSubtotal);

    /**
     * @param mixed $taxInSubtotal
     * @return FeeInterface
     */
    public function setTaxInSubtotal($taxInSubtotal = Excludeinclude::VAR_DEFAULT);

    /**
     * @param mixed $shippingInSubtotal
     * @return FeeInterface
     */
    public function setShippingInSubtotal($shippingInSubtotal);

    /**
     * @param @param string|null $conditionsSerialized
     * @return FeeInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * @param int $sortOrder
     * @return FeeInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * @param int $entityId
     * @return FeeInterface
     */
    public function setId($entityId);

    /**
     * @param mixed $groupId
     * @return FeeInterface
     */
    public function setGroupId($groupId);

    /**
     * @param mixed $storeId
     * @return FeeInterface
     */
    public function setStoreId($storeId);

    /**
     * @param mixed $baseOptions
     * @return FeeInterface
     */
    public function setBaseOptions($baseOptions);

    /**
     * @param bool $flag
     * @return FeeInterface
     */
    public function setIsRequired($flag);

    /**
     * @param bool $flag
     * @return FeeInterface
     */
    public function setIsEligibleForRefund($flag);

    /**
     * @param bool $flag
     * @return FeeInterface
     */
    public function setIsPerProduct(bool $flag);

    /**
     * @param string|null $conditionsSerialized
     * @return FeeInterface
     */
    public function setProductConditionsSerialized(?string $conditionsSerialized);
}
