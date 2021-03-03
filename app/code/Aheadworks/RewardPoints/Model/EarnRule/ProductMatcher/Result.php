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
namespace Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher;

use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\Result\Item;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class Result
 * @package Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher
 * @codeCoverageIgnore
 */
class Result extends AbstractSimpleObject
{
    /**#@+
     * Constants for keys.
     */
    const ITEMS    = 'items';
    const TOTAL_COUNT   = 'total_count';
    /**#@-*/

    /**
     * Get items
     *
     * @return Item[]
     */
    public function getItems()
    {
        return $this->_get(self::ITEMS);
    }

    /**
     * Set items
     *
     * @param Item[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get(self::TOTAL_COUNT);
    }

    /**
     * Set total count
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this->setData(self::TOTAL_COUNT, $totalCount);
    }
}
