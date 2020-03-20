<?php

namespace Icube\Cashback\Plugin;

use Magento\SalesRule\Model\Rule\Metadata\ValueProvider as SalesRuleValueProvider;

class ValueProvider
{

    protected $cashbackDataHelper;

    public function __construct(
        \Icube\Cashback\Helper\Data $cashbackDataHelper
    ) {
        $this->cashbackDataHelper = $cashbackDataHelper;
    }

    public function afterGetMetadataValues(
        SalesRuleValueProvider $subject,
        $result
    ) {
        $actions = &$result['actions']['children']['simple_action']['arguments']['data']['config']['options'];
        $actions = array_merge($actions, $this->cashbackDataHelper->getCashbackTypes());
        return $result;
    }
}
