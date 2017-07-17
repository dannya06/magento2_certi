<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Source;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\Data\GroupInterface;

/**
 * Class CustomerGroup
 *
 * @package Aheadworks\AdvancedReports\Model\Source
 */
class CustomerGroup implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var []
     */
    private $options;

    /**
     * @param GroupManagementInterface $groupManagement
     */
    public function __construct(
        GroupManagementInterface $groupManagement
    ) {
        $this->groupManagement = $groupManagement;
    }

    /**
     * Get options
     *
     * @return []
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            /** @var GroupInterface $notLoggedInGroup */
            $notLoggedInGroup = $this->groupManagement->getNotLoggedInGroup();
            $this->options[] = ['value' => $notLoggedInGroup->getId(), 'label' => $notLoggedInGroup->getCode()];

            /** @var GroupInterface $group */
            foreach ($this->groupManagement->getLoggedInGroups() as $group) {
                $this->options[] = ['value' => $group->getId(), 'label' => $group->getCode()];
            }
        }
        return $this->options;
    }

    /**
     * Get options
     *
     * @return []
     */
    public function getOptions()
    {
        $options = $this->toOptionArray();
        $result = [];

        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }

        return $result;
    }

    /**
     * Get option by value
     *
     * @param int $value
     * @return null
     */
    public function getOptionByValue($value)
    {
        $options = $this->getOptions();
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }
        return null;
    }
}
