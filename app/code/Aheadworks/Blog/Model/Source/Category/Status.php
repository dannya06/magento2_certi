<?php
namespace Aheadworks\Blog\Model\Source\Category;

/**
 * Category Status source model
 * @package Aheadworks\Blog\Model\Source\Category
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    const ENABLED = 1;
    const DISABLED = 0;

    const ENABLED_LABEL = 'Enabled';
    const DISABLED_LABEL = 'Disabled';

    /**
     * @var null|array
     */
    protected $optionArray = null;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->optionArray === null) {
            $this->optionArray = [
                [
                    'value' => self::DISABLED,
                    'label' => __(self::DISABLED_LABEL)
                ],
                [
                    'value' => self::ENABLED,
                    'label' => __(self::ENABLED_LABEL)
                ]
            ];
        }
        return $this->optionArray;
    }
}
