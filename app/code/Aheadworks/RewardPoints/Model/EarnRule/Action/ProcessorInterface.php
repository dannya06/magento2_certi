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
namespace Aheadworks\RewardPoints\Model\EarnRule\Action;

/**
 * Interface ProcessorInterface
 * @package Aheadworks\RewardPoints\Model\EarnRule\Action
 */
interface ProcessorInterface
{
    /**
     * @param float $value
     * @param float $qty
     * @param \Magento\Framework\Api\AttributeInterface[] $attributes
     * @return float
     */
    public function process($value, $qty, $attributes);
}
