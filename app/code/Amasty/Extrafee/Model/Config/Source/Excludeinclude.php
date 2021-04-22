<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Excludeinclude implements OptionSourceInterface
{
    const VAR_EXCLUDE = '0';
    const VAR_INCLUDE = '1';
    const VAR_DEFAULT = '2';

    protected $useDefaultOption = false;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options =  [self::VAR_EXCLUDE => __('No'), self::VAR_INCLUDE => __('Yes')];

        if ($this->useDefaultOption) {
            $options[self::VAR_DEFAULT] = __('Default');
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();

        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $optionArray;
    }

    /**
     * @param bool $useDefaultOption
     * @return $this
     */
    public function setUseDefaultOption($useDefaultOption)
    {
        $this->useDefaultOption = $useDefaultOption;
        return $this;
    }
}
