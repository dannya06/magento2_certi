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
namespace Aheadworks\SocialLogin\Api;

/**
 * Account Search Interface
 */
interface AccountSearchInterface
{
    /**
     * Get Account by social id.
     *
     * @param string $type
     * @param string $socialId
     * @param int|null $websiteId
     * @return \Aheadworks\SocialLogin\Api\Data\AccountInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySocialId($type, $socialId, $websiteId = null);
}
