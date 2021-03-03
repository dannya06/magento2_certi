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
namespace Aheadworks\RewardPoints\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface EarnRateSearchResultsInterface
 * @package Aheadworks\RewardPoints\Api\Data
 * @api
 */
interface EarnRateSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ruratele list
     *
     * @return \Aheadworks\RewardPoints\Api\Data\EarnRateInterface[]
     */
    public function getItems();

    /**
     * Set rate list
     *
     * @param \Aheadworks\RewardPoints\Api\Data\EarnRateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
