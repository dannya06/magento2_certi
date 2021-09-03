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
namespace Aheadworks\SocialLogin\Model\Provider\ServiceBuilder\Provider;

use Aheadworks\SocialLogin\Model\Config\Provider\Paypal as PaypalConfig;
use Aheadworks\SocialLogin\Model\Provider\Service\Config\ConfigInterface;
use Aheadworks\SocialLogin\Model\Provider\Service\Credentials\CredentialsInterface;
use Aheadworks\SocialLogin\Model\Provider\Service\Storage\StorageInterface;
use Aheadworks\SocialLogin\Model\Provider\ServiceBuilder\OAuth2;
use Magento\Framework\ObjectManager\ObjectManager;

/**
 * Paypal Service builder
 */
class Paypal extends OAuth2
{
    /**
     * @param ObjectManager $objectManager
     * @param StorageInterface $storage
     * @param CredentialsInterface $credentials
     * @param ConfigInterface $config
     * @param string $service
     * @param string $sandboxService
     * @param PaypalConfig $paypalConfig
     */
    public function __construct(
        ObjectManager $objectManager,
        StorageInterface $storage,
        CredentialsInterface $credentials,
        ConfigInterface $config,
        $service,
        $sandboxService,
        PaypalConfig $paypalConfig
    ) {
        $service = $paypalConfig->isSandboxModeEnabled() ? $sandboxService : $service;

        parent::__construct($objectManager, $storage, $credentials, $config, $service);
    }
}
