<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\Rule;

use Amasty\Extrafee\Api\Data\FeeInterface;

class RuleRepository
{
    /**
     * @var FeeConditionProcessor[]
     */
    private $storage = [];

    /**
     * @var FeeConditionProcessorFactory
     */
    private $conditionProcessorFactory;

    public function __construct(FeeConditionProcessorFactory $conditionProcessorFactory)
    {
        $this->conditionProcessorFactory = $conditionProcessorFactory;
    }

    /**
     * @param FeeInterface $fee
     *
     * @return FeeConditionProcessor
     */
    public function getByFee(FeeInterface $fee): FeeConditionProcessor
    {
        $key = (int)$fee->getId();
        if (!isset($this->storage[$key])) {
            $this->storage[$key] = $this->conditionProcessorFactory->create(
                ['data' =>
                     [
                         'conditions_serialized' => $fee->getConditionsSerialized(),
                         'actions_serialized' => $fee->getProductConditionsSerialized(),
                     ]
                ]
            );
        }

        return $this->storage[$key];
    }
}
