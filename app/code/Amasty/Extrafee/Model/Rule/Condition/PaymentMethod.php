<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\Rule\Condition;

use Magento\Framework\Model\AbstractModel;
use Magento\Payment\Model\Config\Source\Allmethods;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

class PaymentMethod extends AbstractCondition
{

    /**
     * @var Allmethods
     */
    private $allMethods;

    public function __construct(
        Context $context,
        Allmethods $allMethods,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->allMethods = $allMethods;
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'select';
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
            . __(sprintf(__('Payment Method') . ' %s %s', $this->getOperatorElementHtml(), $value))
            . $this->getRemoveLinkHtml();
    }

    /**
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        $this->setAttribute('payment_method');

        return parent::validate($model);
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData('value_select_options', $this->allMethods->toOptionArray());
        }

        return $this->getData('value_select_options');
    }
}
