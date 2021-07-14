<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Cart;

use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\Fee;
use Amasty\Extrafee\Model\FeesInformationManagement;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

class LayoutProcessor implements LayoutProcessorInterface
{
    /** @var FeesInformationManagement  */
    protected $feesInformationManagement;

    /** @var  CheckoutSession */
    protected $checkoutSession;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /** @var array  */
    protected $components = [
        Fee::FRONTEND_TYPE_DROPDOWN => 'Amasty_Extrafee/js/fee/item/dropdown',
        Fee::FRONTEND_TYPE_CHECKBOX => 'Amasty_Extrafee/js/fee/item/checkbox',
        Fee::FRONTEND_TYPE_RADIO => 'Amasty_Extrafee/js/fee/item'
    ];

    public function __construct(
        FeesInformationManagement $feesInformationManagement,
        CheckoutSession $checkoutSession,
        ConfigProvider $configProvider
    ) {
        $this->feesInformationManagement = $feesInformationManagement;
        $this->checkoutSession = $checkoutSession;
        $this->configProvider = $configProvider;
    }

    /**
     * Process js Layout of block
     * workaround solution for preload necessary options
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if ($this->configProvider->isShowOnCart()) {
            if (isset(
                $jsLayout['components']['block-amasty-extrafee-summary']['children']
                ['block-amasty-extrafee']['children']['amasty-extrafee-fieldsets']['children']
            )) {
                $pointer = &
                    $jsLayout['components']['block-amasty-extrafee-summary']['children']['block-amasty-extrafee']
                    ['children']['amasty-extrafee-fieldsets']['children'];

                $this->prepareExtraFees($pointer);
            }
        }

        if (isset(
            $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']
            ['block-amasty-extrafee-summary']['children']['block-amasty-extrafee']['children']
            ['amasty-extrafee-fieldsets']['children']
        )) {
            $pointer = &$jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']
            ['block-amasty-extrafee-summary']['children']['block-amasty-extrafee']['children']
            ['amasty-extrafee-fieldsets']['children'];

            $this->prepareExtraFees($pointer);
        }

        return $jsLayout;
    }

    /**
     * @param array $elements
     */
    private function prepareExtraFees(array &$elements)
    {
        $feeItems = $this->feesInformationManagement->collectQuote($this->checkoutSession->getQuote());

        /** @var \Amasty\Extrafee\Model\Fee $fee */
        foreach ($feeItems as $fee) {
            if ($fee->getEntityId()) {
                $id = $fee->getEntityId();
            } else {
                $id = $fee->getId();
            } //don't remove. appearance fees on cart page bugfix
            if (array_key_exists($fee->getFrontendType(), $this->components)) {
                $elements['fee.' . $id] = [
                    'parent' => '${ $.name }',
                    'name' => '${ $.name }.fee.' . $id,
                    'description' => $fee->getDescription(),
                    'component' => $this->components[$fee->getFrontendType()],
                    'provider' => 'checkoutProvider',
                    'options' => $fee->getBaseOptions(),
                    'label' => $fee->getName(),
                    'frontendType' => $fee->getFrontendType(),
                    'customScope' => 'amastyExtrafee',
                    'feeId' => $id,
                    'value' => $fee->getCurrentValue(),
                    'validation' => ['required-entry' => $fee->isRequired()]
                ];
            }
        }
    }
}
