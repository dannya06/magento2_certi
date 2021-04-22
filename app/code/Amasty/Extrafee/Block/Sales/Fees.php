<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Block\Sales;

use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\ExtrafeeOrder;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Block\Adminhtml\Order\Invoice\Totals;
use Magento\Tax\Model\Config;

class Fees extends Template
{
    const FEE_CODE = 'amasty_extrafee';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    /**
     * @param Totals $parent
     * @param ExtrafeeOrder $feeObjectWithOrder
     * @return $this
     */
    public function getFees($parent, $feeObjectWithOrder)
    {
        $displayPrices = $this->configProvider->displaySalesPrices();

        $feeAmount = $feeObjectWithOrder->getTotalAmount();
        $baseFeeAmount = $feeObjectWithOrder->getBaseTotalAmount();
        $taxAmount = $feeObjectWithOrder->getTaxAmount();
        $baseTaxAmount = $feeObjectWithOrder->getBaseTaxAmount();
        $feeId = $feeObjectWithOrder->getFeeId();
        $feeOptionId = $feeObjectWithOrder->getOptionId();
        $feeLabel = $feeObjectWithOrder->getLabel();
        $feeOptionLabel = $feeObjectWithOrder->getOptionLabel();

        if ($feeAmount >= 0) {
            if ($displayPrices == Config::DISPLAY_TYPE_BOTH) {
                $code = self::FEE_CODE . '_excl_tax_' . $feeId . '_' . $feeOptionId;
                $this->addTotal($parent, $code, '(Excl.Tax)', $feeAmount, $baseFeeAmount, $feeLabel, $feeOptionLabel);

                $feeAmount += $taxAmount;
                $baseFeeAmount += $baseTaxAmount;

                $code = self::FEE_CODE . '_incl_tax_' . $feeId . '_' . $feeOptionId;
                $this->addTotal($parent, $code, '(Incl.Tax)', $feeAmount, $baseFeeAmount, $feeLabel, $feeOptionLabel);
            } else {
                if ($displayPrices == Config::DISPLAY_TYPE_INCLUDING_TAX) {
                    $feeAmount += $taxAmount;
                    $baseFeeAmount += $baseTaxAmount;
                }

                $code = self::FEE_CODE . '_' . $feeId . '_' . $feeOptionId;
                $this->addTotal($parent, $code, '', $feeAmount, $baseFeeAmount, $feeLabel, $feeOptionLabel);
            }
        }

        return $this;
    }

    /**
     * @param Totals $parent
     * @param string $code
     * @param string $taxDisplay
     * @param float $feeAmount
     * @param float $baseFeeAmount
     * @param string $feeLabel
     * @param string $feeOptionLabel
     */
    public function addTotal($parent, $code, $taxDisplay, $feeAmount, $baseFeeAmount, $feeLabel, $feeOptionLabel)
    {
        $fee = new \Magento\Framework\DataObject(
            [
                'code' => $code,
                'strong' => false,
                'value' => $feeAmount,
                'base_value' => $baseFeeAmount,
                'label' => __('Extra Fee %1: %2 (%3)', $taxDisplay, $feeLabel, $feeOptionLabel),
            ]
        );

        $parent->addTotal($fee, 'shipping');
    }
}
