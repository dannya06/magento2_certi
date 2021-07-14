<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\Rule\Condition;

use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

class BillingAddressCountry extends AbstractCondition
{
    /**
     * @var Country
     */
    private $country;

    public function __construct(
        Context $context,
        Country $country,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->country = $country;
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
            . __(sprintf(__('Billing Address Country') . ' %s %s', $this->getOperatorElementHtml(), $value))
            . $this->getRemoveLinkHtml();
    }

    /**
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        if (!$model->getSameAsBilling()) {
            $model->setData('billing_country', $model->getQuote()->getBillingAddress()->getCountryId());
        } else {
            $model->setData('billing_country', $model->getCountryId());
        }
        $this->setAttribute('billing_country');

        return parent::validate($model);
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData('value_select_options', $this->country->toOptionArray());
        }

        return $this->getData('value_select_options');
    }
}
