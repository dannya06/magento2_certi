<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\Pdf;

use Amasty\Extrafee\Api\Data\ExtrafeeCreditmemoInterface;
use Amasty\Extrafee\Api\Data\ExtrafeeInvoiceInterface;
use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeCreditmemo\CollectionFactory as FeeCreditmemoCollectionFactory;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeInvoice\CollectionFactory as FeeInvoiceCollectionFactory;
use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Config;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory as TaxCollectionFactory;

class Fee extends DefaultTotal
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var FeeInvoiceCollectionFactory
     */
    private $feeInvoiceCollectionFactory;

    /**
     * @var FeeCreditmemoCollectionFactory
     */
    private $feeCreditmemoCollectionFactory;

    public function __construct(
        Data $taxHelper,
        Calculation $taxCalculation,
        TaxCollectionFactory $ordersFactory,
        ConfigProvider $configProvider,
        FeeInvoiceCollectionFactory $feeInvoiceCollectionFactory,
        FeeCreditmemoCollectionFactory $feeCreditmemoCollectionFactory,
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        $this->feeInvoiceCollectionFactory = $feeInvoiceCollectionFactory;
        $this->feeCreditmemoCollectionFactory = $feeCreditmemoCollectionFactory;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * @return array
     */
    public function getTotalsForDisplay(): array
    {
        $totals = [];
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $displayPrices = $this->configProvider->displaySalesPrices();

        if ($this->getSource()->getEntityType() === 'creditmemo') {
            $feeCollectionWithOrder = $this->feeCreditmemoCollectionFactory->create()
                ->addFieldToFilter(ExtrafeeCreditmemoInterface::CREDITMEMO_ID, $this->getSource()->getId());
        } else {
            $feeCollectionWithOrder = $this->feeInvoiceCollectionFactory->create()
                ->addFieldToFilter(ExtrafeeInvoiceInterface::INVOICE_ID, $this->getSource()->getId());
        }

        foreach ($feeCollectionWithOrder->getItems() as $feeWithOrder) {
            $feeAmount = $feeWithOrder->getTotalAmount();
            $taxAmount = $feeWithOrder->getTaxAmount();
            $feeLabel = $feeWithOrder->getLabel();
            $feeOptionLabel = $feeWithOrder->getOptionLabel();

            if ($displayPrices == Config::DISPLAY_TYPE_BOTH) {
                $totals[] = [
                    'amount' => $this->getOrder()->formatPriceTxt($feeAmount),
                    'label' => __('Extra Fee (Excl.Tax)') . ': ' . $feeLabel . ' (' . $feeOptionLabel . ')',
                    'font_size' => $fontSize,
                ];

                $totals[] = [
                    'amount' => $this->getOrder()->formatPriceTxt($feeAmount + $taxAmount),
                    'label' => __('Extra Fee (Incl.Tax)') . ': ' . $feeLabel . ' (' . $feeOptionLabel . ')',
                    'font_size' => $fontSize,
                ];
            } else {
                if ($displayPrices == Config::DISPLAY_TYPE_INCLUDING_TAX) {
                    $feeAmount += $taxAmount;
                }
                $totals[] = [
                    'amount' => $this->getOrder()->formatPriceTxt($feeAmount),
                    'label' => __('Extra Fee') . ': ' . $feeLabel . ' (' . $feeOptionLabel . ')',
                    'font_size' => $fontSize,
                ];
            }
        }

        return $totals;
    }
}
