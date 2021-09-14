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
namespace Aheadworks\SocialLogin\Test\TestCase;

use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. @TODO
 *
 * Steps:
 * 1. Set login block settings
 * 2. Flush cache
 * 3. Go to customer login page
 * 4. Click login via %provider%
 * 5. Submit credentials
 * 6. Go to customer account Social Accounts tab
 * 7. Assert linked account
 *
 * @group @TODO
 */
class CustomerSocialLoginTest extends Scenario
{
    /**
     * Run scenario
     */
    public function test()
    {
        $this->executeScenario();
    }
}
