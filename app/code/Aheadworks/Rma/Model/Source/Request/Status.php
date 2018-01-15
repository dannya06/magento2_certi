<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Model\Source\Request;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 *
 * @package Aheadworks\Rma\Model\Source\Request
 */
class Status implements ArrayInterface
{
    /**#@+
     * Constants defined for RMA status
     */
    const APPROVED = 1;
    const CANCELED = 2;
    const CLOSED = 3;
    const ISSUE_REFUND = 4;
    const PACKAGE_RECEIVED = 5;
    const PACKAGE_SENT = 6;
    const PENDING_APPROVAL = 7;
    /**#@-*/

    /**
     * @var array
     */
    private $options;

    /**
     * Retrieve options without translation
     *
     * @return array
     */
    public function getOptionsWithoutTranslation()
    {
        return [
            ['value' => self::PENDING_APPROVAL, 'label' => 'Pending Approval'],
            ['value' => self::APPROVED, 'label' => 'Approved'],
            ['value' => self::PACKAGE_SENT, 'label' => 'Package Sent'],
            ['value' => self::PACKAGE_RECEIVED, 'label' => 'Package Received'],
            ['value' => self::ISSUE_REFUND, 'label' => 'Issue Refund'],
            ['value' => self::CLOSED, 'label' => 'Closed'],
            ['value' => self::CANCELED, 'label' => 'Canceled']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            foreach ($this->getOptionsWithoutTranslation() as $option) {
                $this->options[] = ['value' => $option['value'], 'label' => __($option['label'])];
            }
        }
        return $this->options;
    }

    /**
     * Retrieve option by value
     *
     * @param int $value
     * @param bool $translate
     * @return string|null
     */
    public function getOptionLabelByValue($value, $translate = true)
    {
        $options = $translate
            ? $this->toOptionArray()
            : $this->getOptionsWithoutTranslation();

        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return null;
    }
}
