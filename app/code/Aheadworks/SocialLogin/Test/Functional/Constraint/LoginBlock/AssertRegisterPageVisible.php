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
namespace Aheadworks\SocialLogin\Test\Constraint\LoginBlock;

use Magento\Customer\Test\Page\CustomerAccountCreate;

/**
 * Class AssertRegisterPageVisible
 */
class AssertRegisterPageVisible extends AssertVisible
{
    /**
     * @param CustomerAccountCreate $customerAccountCreate
     */
    public function processAssert(
        CustomerAccountCreate $customerAccountCreate
    ) {
        $this->processVisibleAssert($customerAccountCreate);
    }
}
