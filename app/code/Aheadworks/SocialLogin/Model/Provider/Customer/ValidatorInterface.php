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
 * @package    SocialLogin
 * @version    1.6.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\SocialLogin\Model\Provider\Customer;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Interface ValidatorInterface
 */
interface ValidatorInterface
{
    /**#@+
     * Error types
     */
    const ERROR_TYPE_EMPTY_FIELD = 'empty_field';
    const ERROR_TYPE_INVALID_FIELD = 'invalid_field';
    /**#@-*/
    
    /**
     * Validate customer data
     *
     * @param CustomerInterface $customer
     * @return string[] invalid fields
     */
    public function validate(CustomerInterface $customer);

    /**
     * Is customer valid
     *
     * @param CustomerInterface $customer
     * @return boolean
     */
    public function isValid(CustomerInterface $customer);
}
