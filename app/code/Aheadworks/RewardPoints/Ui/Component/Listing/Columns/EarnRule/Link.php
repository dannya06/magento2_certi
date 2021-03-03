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
namespace Aheadworks\RewardPoints\Ui\Component\Listing\Columns\EarnRule;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Link
 * @package Aheadworks\RewardPoints\Ui\Component\Listing\Columns\EarnRule
 */
class Link extends Column
{
    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['link'])) {
                $item['link'] = $this->context->getUrl(
                    'aw_reward_points/earning_rules/edit',
                    ['id' => $item[EarnRuleInterface::ID]]
                );
            }
        }

        return $dataSource;
    }
}
