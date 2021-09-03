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
namespace Aheadworks\SocialLogin\Model\Config\Provider;

use Aheadworks\SocialLogin\Model\Config\Provider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Paypal config.
 */
class Paypal
{
    /**
     * Path to sandbox config.
     */
    const XML_PATH_PROVIDER_IS_SANDBOX_MODE_ENABLED = 'social/paypal/is_sandbox_mode_enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(ScopeConfigInterface $scopeConfigInterface)
    {
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * Is sandbox mode enabled.
     *
     * @return bool
     */
    public function isSandboxModeEnabled()
    {
        return $this->scopeConfigInterface->isSetFlag(
            self::XML_PATH_PROVIDER_IS_SANDBOX_MODE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
