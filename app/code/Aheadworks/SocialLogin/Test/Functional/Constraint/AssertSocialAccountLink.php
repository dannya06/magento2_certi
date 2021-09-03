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
namespace Aheadworks\SocialLogin\Test\Constraint;

use Aheadworks\SocialLogin\Test\Page\CustomerSocialAccountList;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertLoginLinkVisible
 */
class AssertSocialAccountLink extends AbstractConstraint
{
    /**
     * @param CustomerSocialAccountList $socialAccountList
     * @param string $providerName
     */
    public function processAssert(
        CustomerSocialAccountList $socialAccountList,
        $providerName
    ) {
        //@TODO refactor to waitUntil
        sleep(5);
        $socialAccountList->open();
        $isLinkedAccount = $socialAccountList->getLinkedAccountsBlock()->isAccountExist($providerName);

        \PHPUnit_Framework_Assert::assertTrue(
            $isLinkedAccount
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Social account doesn\'t linked to customer.';
    }
}
