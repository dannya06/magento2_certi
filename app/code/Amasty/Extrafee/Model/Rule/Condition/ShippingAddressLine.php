<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\Rule\Condition;

use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;

class ShippingAddressLine extends AbstractCondition
{

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'string';
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        try {
            $value = $this->getValueElementHtml();
        } catch (\Exception $e) {
            $value = '';
        }

        return $this->getTypeElementHtml()
            . __(sprintf(__('Shipping Address Line') . ' %s %s', $this->getOperatorElementHtml(), $value))
            . $this->getRemoveLinkHtml();
    }

    /**
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        $model->setData('dest_street', $model->getStreetFull());
        $this->setAttribute('dest_street');

        return parent::validate($model);
    }

    /**
     * @return array
     */
    public function getOperatorSelectOptions()
    {
        $operators = [
            '{}' => __('contains'),
            '!{}' => __('does not contain'),
        ];

        $type = $this->getInputType();
        $result = [];
        $operatorByType = $this->getOperatorByInputType();

        foreach ($operators as $operatorKey => $operatorValue) {
            if (!$operatorByType || in_array($operatorKey, $operatorByType[$type])) {
                $result[] = ['value' => $operatorKey, 'label' => $operatorValue];
            }
        }

        return $result;
    }
}
