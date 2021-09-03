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
namespace Aheadworks\SocialLogin\Test\Block;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 */
class SocialLogin extends Block
{
    /**
     * @var string
     */
    protected $providerLoginLinkLabel = 'a.social-login-btn-%s';

    /**
     * Is visible login button
     *
     * @param string $providerCode
     * @return bool
     */
    public function isVisibleLoginBy($providerCode)
    {
        return $this->getLoginLink($providerCode)->isVisible();
    }

    /**
     * Click login by provider
     *
     * @param string $providerCode
     * @return void
     */
    public function clickLoginBy($providerCode)
    {
        $this->getLoginLink($providerCode)->click();
    }

    /**
     * @param $providerCode
     * @return \Magento\Mtf\Client\ElementInterface
     */
    protected function getLoginLink($providerCode)
    {
        return $this->_rootElement->find(
            sprintf($this->providerLoginLinkLabel, $providerCode),
            Locator::SELECTOR_CSS
        );
    }
}
