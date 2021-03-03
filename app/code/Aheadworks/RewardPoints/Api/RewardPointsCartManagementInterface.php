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
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * @api
 */
interface RewardPointsCartManagementInterface
{
    /**
     * Returns information for a reward point in a specified cart.
     *
     * @param  int $cartId
     * @return boolean
     * @throws NoSuchEntityException The specified cart does not exist.
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function get($cartId);

    /**
     * Adds a reward points to a specified cart.
     *
     * @param  int $cartId
     * @return mixed
     * @throws NoSuchEntityException The specified cart does not exist.
     * @throws CouldNotSaveException The specified reward points not be added.
     */
    public function set($cartId);

    /**
     * Deletes a reward points from a specified cart.
     *
     * @param  int $cartId
     * @return boolean
     * @throws NoSuchEntityException The specified cart does not exist.
     * @throws CouldNotDeleteException The specified reward points could not be deleted.
     */
    public function remove($cartId);
}
