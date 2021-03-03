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
namespace Aheadworks\RewardPoints\Model\Calculator;

use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class Result
 * @package Aheadworks\RewardPoints\Model\Calculator
 * @codeCoverageIgnore
 */
class Result extends AbstractSimpleObject implements ResultInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        return $this->_get(self::POINTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPoints($points)
    {
        return $this->setData(self::POINTS, $points);
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedRuleIds()
    {
        return $this->_get(self::APPLIED_RULE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedRuleIds($ruleIds)
    {
        return $this->setData(self::APPLIED_RULE_IDS, $ruleIds);
    }
}
