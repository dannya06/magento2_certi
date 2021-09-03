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
namespace Aheadworks\SocialLogin\Test\TestStep;

use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Class SocialLoginStep
 */
class SocialLoginStep implements TestStepInterface
{
    /**
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * @var string
     */
    protected $providerName;

    /**
     * @param CustomerAccountLogin $customerAccountLogin
     * @param string $providerName
     */
    public function __construct(
        CustomerAccountLogin $customerAccountLogin,
        $providerName
    ) {
        $this->customerAccountLogin = $customerAccountLogin;
        $this->providerName = $providerName;
    }

    /**
     * Customer login via social
     *
     * @return void
     */
    public function run()
    {
        $this->customerAccountLogin->open();
        $this->customerAccountLogin->getSocialLoginBlock()->clickLoginBy($this->providerName);
    }
}
