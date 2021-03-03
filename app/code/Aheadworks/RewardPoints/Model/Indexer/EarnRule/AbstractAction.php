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

use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product as EarnRuleProductIndexerResource;

/**
 * Class AbstractAction
 * @package Aheadworks\RewardPoints\Model\Indexer\EarnRule
 */
abstract class AbstractAction
{
    /**
     * @var EarnRuleProductIndexerResource
     */
    protected $earnRuleProductIndexerResource;

    /**
     * @param EarnRuleProductIndexerResource $earnRuleProductIndexerResource
     */
    public function __construct(
        EarnRuleProductIndexerResource $earnRuleProductIndexerResource
    ) {
        $this->earnRuleProductIndexerResource = $earnRuleProductIndexerResource;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     * @return void
     */
    abstract public function execute($ids);
}
