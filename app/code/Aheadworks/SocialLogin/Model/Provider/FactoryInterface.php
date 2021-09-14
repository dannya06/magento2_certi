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
namespace Aheadworks\SocialLogin\Model\Provider;

use Aheadworks\SocialLogin\Model\Config\ProviderInterface as ProviderConfig;
use Aheadworks\SocialLogin\Model\Provider\RequestProcessor\CallbackInterface;
use Aheadworks\SocialLogin\Model\Provider\RequestProcessor\LoginInterface;

/**
 * Interface FactoryInterface
 */
interface FactoryInterface
{
    /**
     * Create Provider service
     *
     * @return mixed
     */
    public function createService();

    /**
     * Get config
     *
     * @return ProviderConfig
     */
    public function getConfig();

    /**
     * Create callback request processor
     *
     * @return CallbackInterface
     */
    public function createCallbackRequestProcessor();

    /**
     * Create login request processor
     *
     * @return LoginInterface
     */
    public function createLoginRequestProcessor();
}
