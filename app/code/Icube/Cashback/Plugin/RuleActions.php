<?php

namespace Icube\Cashback\Plugin;

use Amasty\Rgrid\Model\RuleActions as RuleActionsProvider;

class RuleActions
{
    private $cashbackDataHelper;

    public function __construct(\Icube\Cashback\Helper\Data $cashbackDataHelper)
    {
        $this->cashbackDataHelper = $cashbackDataHelper;
    }

    public function afterToOptionArray(
        RuleActionsProvider $subject,
        $result
    ) {
        $result = array_merge($result,$this->cashbackDataHelper->getStaticCashbackTypes());
        return $result;
    }
}
