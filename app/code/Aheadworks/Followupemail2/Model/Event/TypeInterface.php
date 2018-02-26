<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Model\Event;

/**
 * Interface TypeInterface
 * @package Aheadworks\Followupemail2\Model\Event
 */
interface TypeInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ENABLED               = 'enabled';
    const CODE                  = 'code';
    const TITLE                 = 'title';
    const DESCRIPTION           = 'description';
    const HANDLER               = 'handler';
    const CUSTOMER_CONDITIONS   = 'customer_conditions';
    const CART_CONDITIONS       = 'cart_conditions';
    const ORDER_CONDITIONS      = 'order_conditions';
    const PRODUCT_CONDITIONS    = 'product_conditions';
    const ALLOWED_FOR_GUESTS    = 'allowed_for_guests';
    /**#@-*/

    /**
     * Check if type is enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Get type code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get type title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get type description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get type handler
     *
     * @return HandlerInterface
     */
    public function getHandler();

    /**
     * Check if customer conditions is enabled
     *
     * @return bool
     */
    public function isCustomerConditionsEnabled();

    /**
     * Check if cart conditions is enabled
     *
     * @return bool
     */
    public function isCartConditionsEnabled();

    /**
     * Check if order conditions is enabled
     *
     * @return bool
     */
    public function isOrderConditionsEnabled();

    /**
     * Check if product conditions is enabled
     *
     * @return bool
     */
    public function isProductConditionsEnabled();

    /**
     * Is element enabled
     *
     * @param string $element
     * @return bool
     */
    public function isElementEnabled($element);
}
