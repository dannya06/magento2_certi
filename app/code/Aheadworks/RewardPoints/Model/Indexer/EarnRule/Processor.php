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
namespace Aheadworks\RewardPoints\Model\Indexer\EarnRule;

use Magento\Framework\Indexer\AbstractProcessor;

/**
 * Class Processor
 * @package Aheadworks\RewardPoints\Model\Indexer\EarnRule
 * @codeCoverageIgnore
 */
class Processor extends AbstractProcessor
{
    /**
     * {@inheritdoc}
     */
    const INDEXER_ID = 'aw_reward_points_earn_rule';
}
