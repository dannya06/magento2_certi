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
namespace Aheadworks\SocialLogin\Model\Provider\Account;

use Aheadworks\SocialLogin\Model\Provider\AccountInterface as ProviderAccountInterface;
use Aheadworks\SocialLogin\Api\Data\AccountInterface;

/**
 * Interface ConverterInterface
 */
interface ConverterInterface
{
    /**
     * Convert provider account to social account
     *
     * @param ProviderAccountInterface $providerAccount
     * @return AccountInterface
     */
    public function convert(ProviderAccountInterface $providerAccount);
}
