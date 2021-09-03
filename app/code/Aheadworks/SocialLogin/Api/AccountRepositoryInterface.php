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

use Aheadworks\SocialLogin\Api\Data\AccountInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Account Repository Interface
 */
interface AccountRepositoryInterface
{
    /**
     * Save Account
     * @param Data\AccountInterface $account
     * @return AccountInterface
     * @throws CouldNotSaveException
     */
    public function save(AccountInterface $account);

    /**
     * Get Account by id
     * @param int $accountId
     * @return AccountInterface
     * @throws NoSuchEntityException
     */
    public function get($accountId);

    /**
     * Get Account by id
     * @param string $type
     * @param string $socialId
     * @param int|null $websiteId
     * @return AccountInterface
     * @throws NoSuchEntityException
     * @deprecated
     * @see \Aheadworks\SocialLogin\Api\AccountSearchInterface
     */
    public function getBySocialId($type, $socialId, $websiteId = null);

    /**
     * Delete Account
     * @param AccountInterface $account
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(AccountInterface $account);
}
