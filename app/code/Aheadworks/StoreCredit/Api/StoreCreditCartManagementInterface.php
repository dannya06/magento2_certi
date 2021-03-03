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
 * @package    StoreCredit
 * @version    1.1.7
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\StoreCredit\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * @api
 */
interface StoreCreditCartManagementInterface
{
    /**
     * Returns information for a Store Credit in a specified cart
     *
     * @param  int $cartId
     * @return boolean
     * @throws NoSuchEntityException The specified cart does not exist
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function get($cartId);

    /**
     * Adds a Store Credit to a specified cart.
     *
     * @param  int $cartId
     * @return boolean
     * @throws NoSuchEntityException The specified cart does not exist
     * @throws CouldNotSaveException The specified Store Credit not be added
     */
    public function set($cartId);

    /**
     * Deletes a Store Credit from a specified cart
     *
     * @param  int $cartId
     * @return boolean
     * @throws NoSuchEntityException The specified cart does not exist
     * @throws CouldNotDeleteException The specified Store Credit could not be deleted
     */
    public function remove($cartId);
}
