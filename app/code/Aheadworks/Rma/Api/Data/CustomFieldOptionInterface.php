<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Api\Data;

/**
 * Custom field option interface
 * @api
 */
interface CustomFieldOptionInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const FIELD_ID = 'field_id';
    const SORT_ORDER = 'sort_order';
    const IS_DEFAULT = 'is_default';
    const ENABLED = 'enabled';
    const STORE_LABELS = 'store_labels';
    const STOREFRONT_LABEL = 'storefront_label';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get field ID
     *
     * @return int
     */
    public function getFieldId();

    /**
     * Set field ID
     *
     * @param int $fieldId
     * @return $this
     */
    public function setFieldId($fieldId);

    /**
     * Get order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Is default
     *
     * @return bool
     */
    public function isDefault();

    /**
     * Set is default
     *
     * @param bool $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault);

    /**
     * Get enabled
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getEnabled();

    /**
     * Set enabled
     *
     * @param bool|null $enabled
     * @return bool
     */
    public function setEnabled($enabled);

    /**
     * Get option label for store scopes
     *
     * @return \Aheadworks\Rma\Api\Data\StoreValueInterface[]|null
     */
    public function getStoreLabels();

    /**
     * Set option label for store scopes
     *
     * @param \Aheadworks\Rma\Api\Data\StoreValueInterface[] $storeLabels
     * @return $this
     */
    public function setStoreLabels($storeLabels);

    /**
     * Get storefront label
     *
     * @return string
     */
    public function getStorefrontLabel();

    /**
     * Set storefront label
     *
     * @param string $storefrontLabel
     * @return $this
     */
    public function setStorefrontLabel($storefrontLabel);
}
