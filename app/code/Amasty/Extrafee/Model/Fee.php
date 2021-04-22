<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\Config\Source\Excludeinclude;
use Amasty\Extrafee\Model\ResourceModel\Fee as FeeResource;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Fee main model
 */
class Fee extends AbstractModel implements FeeInterface, IdentityInterface
{
    /**
     * Frontend types
     */
    const FRONTEND_TYPE_CHECKBOX = 'checkbox';
    const FRONTEND_TYPE_DROPDOWN = 'dropdown';
    const FRONTEND_TYPE_RADIO = 'radio';

    /**
     * Price types
     */
    const PRICE_TYPE_FIXED = 'fixed';
    const PRICE_TYPE_PERCENT = 'percent';

    /**
     * Fee cache tag
     */
    const CACHE_TAG = 'amasty_extrafee_fee';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(FeeResource::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return int
     */
    public function getDiscountInSubtotal()
    {
        return parent::getData(self::DISCOUNT_IN_SUBTOTAL);
    }

    /**
     * @return int
     */
    public function getTaxInSubtotal()
    {
        return parent::getData(self::TAX_IN_SUBTOTAL);
    }

    /**
     * @return int
     */
    public function getShippingInSubtotal()
    {
        return parent::getData(self::SHIPPING_IN_SUBTOTAL);
    }

    /**
     * @param $optionId
     * @return array
     */
    public function getOption($optionId)
    {
        $ret = [];
        foreach ($this->getOptions() as $item) {
            if ($item['entity_id'] === $optionId) {
                $ret = $item;
                break;
            }
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getOptionsIds()
    {
        $ids = [];
        foreach ($this->getOptions() as $item) {
            $ids[] = $item['entity_id'];
        }

        return $ids;
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Get enabled
     * @return bool|null
     */
    public function getEnabled()
    {
        return parent::getData(self::ENABLED);
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return parent::getData(self::NAME);
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return parent::getData(self::DESCRIPTION);
    }

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
    public function getOptions()
    {
        return parent::getData(self::OPTIONS);
    }

    /**
     * Get type
     * @return string
     */
    public function getFrontendType()
    {
        return parent::getData(self::FRONTEND_TYPE);
    }

    /**
     * Get current value
     * @return string
     */
    public function getCurrentValue()
    {
        return parent::getData(self::CURRENT_VALUE);
    }

    /**
     * @return string
     */
    public function getConditionsSerialized()
    {
        return parent::getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * @return int|mixed
     */
    public function getSortOrder()
    {
        return parent::getData(self::SORT_ORDER);
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return parent::getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * @return string[]
     */
    public function getStoreId()
    {
        return parent::getData(self::STORE_ID);
    }

    /**
     * @return string
     */
    public function getBaseOptions()
    {
        return $this->getData(self::BASE_OPTIONS);
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool)$this->getData(self::IS_REQUIRED);
    }

    /**
     * @return bool
     */
    public function isPerProduct(): bool
    {
        return (bool)$this->getData(self::IS_PER_PRODUCT);
    }

    /**
     * @return null|string
     */
    public function getProductConditionsSerialized(): ?string
    {
        return parent::getData(self::PRODUCT_CONDITIONS_SERIALIZED);
    }

    /**
     * @param $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        return $this->setData(self::ENABLED, $enabled);
    }

    /**
     * @param string $description
     * @return FeeInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @param string $name
     * @return FeeInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @param array $options
     * @return FeeInterface
     */
    public function setOptions($options)
    {
        return $this->setData(self::OPTIONS, $options);
    }

    /**
     * @param array $options
     * @return FeeInterface
     */
    public function setBaseOptions($options)
    {
        return $this->setData(self::BASE_OPTIONS, $options);
    }

    /**
     * @param string $frontendType
     * @return FeeInterface
     */
    public function setFrontendType($frontendType)
    {
        return $this->setData(self::FRONTEND_TYPE, $frontendType);
    }

    /**
     * @param mixed $currentValue
     * @return FeeInterface
     */
    public function setCurrentValue($currentValue)
    {
        return $this->setData(self::CURRENT_VALUE, $currentValue);
    }

    /**
     * @param mixed $discountInSubtotal
     * @return FeeInterface
     */
    public function setDiscountInSubtotal($discountInSubtotal)
    {
        return $this->setData(self::DISCOUNT_IN_SUBTOTAL, $discountInSubtotal);
    }

    /**
     * @param mixed $taxInSubtotal
     * @return FeeInterface
     */
    public function setTaxInSubtotal($taxInSubtotal = Excludeinclude::VAR_DEFAULT)
    {
        return $this->setData(self::TAX_IN_SUBTOTAL, $taxInSubtotal);
    }

    /**
     * @param mixed $shippingInSubtotal
     * @return FeeInterface
     */
    public function setShippingInSubtotal($shippingInSubtotal)
    {
        return $this->setData(self::SHIPPING_IN_SUBTOTAL, $shippingInSubtotal);
    }

    /**
     * @param string $conditionsSerialized
     * @return $this|FeeInterface
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * @param int $sortOrder
     * @return $this|FeeInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @param int $entityId
     * @return $this|FeeInterface
     */
    public function setId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @param array $storeId
     * @return $this|FeeInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @param array $groupId
     * @return $this|FeeInterface
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $groupId);
    }

    /**
     * @param bool $flag
     * @return FeeInterface|Fee
     */
    public function setIsRequired($flag)
    {
        return $this->setData(self::IS_REQUIRED, $flag);
    }

    /**
     * @param bool $flag
     * @return FeeInterface|Fee
     */
    public function setIsEligibleForRefund($flag)
    {
        return $this->setData(self::IS_ELIGIBLE_REFUND, $flag);
    }

    /**
     * @param bool $flag
     * @return FeeInterface|Fee
     */
    public function setIsPerProduct(bool $flag)
    {
        return $this->setData(self::IS_PER_PRODUCT, $flag);
    }

    /**
     * @param string|null $conditions
     * @return $this|FeeInterface
     */
    public function setProductConditionsSerialized(?string $conditions)
    {
        return $this->setData(self::PRODUCT_CONDITIONS_SERIALIZED, $conditions);
    }
}
