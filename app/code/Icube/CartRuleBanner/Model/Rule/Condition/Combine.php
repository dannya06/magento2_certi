<?php

namespace Icube\CartRuleBanner\Model\Rule\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Address
     */
    protected $_conditionAddress;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\SalesRule\Model\Rule\Condition\Address $conditionAddress
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\SalesRule\Model\Rule\Condition\Address $conditionAddress,
        array $data = []
    ) {
        $this->_eventManager = $eventManager;
        $this->_conditionAddress = $conditionAddress;
        parent::__construct($context, $data);
        $this->setType(\Icube\CartRuleBanner\Model\Rule\Condition\Combine::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $allowed  = ['base_subtotal', 'total_qty'];
        $filteredAddressAttributes = array_filter(
            $addressAttributes,
            function ($key) use ($allowed) {
                return in_array($key, $allowed);
            },
            ARRAY_FILTER_USE_KEY
        );

        $attributes = [];
        foreach ($filteredAddressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Magento\SalesRule\Model\Rule\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Cart Attribute'), 'value' => $attributes]
            ]
        );

        return $conditions;
    }
}
