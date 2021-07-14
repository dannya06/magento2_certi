<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeCreditmemo;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeInvoice;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote;
use Amasty\Extrafee\Model\ResourceModel\Fee;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class QuoteToOrderDataMigration
{
    const ORDER_ID = 'order_id';
    const INVOICE_ID = 'invoice_id';
    const CREDITMEMO_ID = 'creditmemo_id';
    const LABEL = 'fee_label';

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->insertDataToExtrafee($setup, $this->getExtrafeeQuotesWithOrders($setup));
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return array
     */
    private function getExtrafeeQuotesWithOrders(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $salesOrderTable = $setup->getTable('sales_order');
        $salesInvoiceTable = $setup->getTable('sales_invoice');
        $salesCreditmemoTable = $setup->getTable('sales_creditmemo');
        $extrafeeTable = $setup->getTable(Fee::TABLE_NAME);
        $extrafeeQuoteTable = $setup->getTable(ExtrafeeQuote::TABLE_NAME);

        $select = $connection->select()
            ->from($extrafeeQuoteTable)
            ->where('option_id !=(?)', 0)
            ->joinInner(
                ['salesorder' => $salesOrderTable],
                'salesorder.quote_id = ' . $extrafeeQuoteTable . '.quote_id',
                [
                    'salesorder.entity_id as ' . self::ORDER_ID,
                ]
            )
            ->joinLeft(
                ['salesinvoice' => $salesInvoiceTable],
                'salesinvoice.order_id = salesorder.entity_id',
                [
                    'salesinvoice.entity_id as ' . self::INVOICE_ID
                ]
            )
            ->joinLeft(
                ['salescreditmemo' => $salesCreditmemoTable],
                'salescreditmemo.order_id = salesorder.entity_id',
                [
                    'salescreditmemo.entity_id as ' . self::CREDITMEMO_ID
                ]
            )
            ->joinInner(
                ['extrafee' => $extrafeeTable],
                'extrafee.entity_id = ' . $extrafeeQuoteTable . '.fee_id',
                [
                    'extrafee.name as ' . self::LABEL,
                ]
            );

        return $connection->fetchAll($select);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param array $extrafeeQuotesWithOrders
     */
    private function insertDataToExtrafee(ModuleDataSetupInterface $setup, $extrafeeQuotesWithOrders)
    {
        foreach ($extrafeeQuotesWithOrders as $extrafeeQuote) {
            $isRefunded = false;
            $baseTotalAmountInvoiced = $totalAmountInvoiced = $baseTaxAmountInvoiced = $taxAmountInvoiced = 0;
            $baseTotalAmountRefunded = $totalAmountRefunded = $baseTaxAmountRefunded = $taxAmountRefunded = 0;

            if (empty($extrafeeQuote[self::LABEL])) {
                $extrafeeQuote[self::LABEL] = 'Surcharge';
            }

            if (!empty($extrafeeQuote[self::INVOICE_ID])) {
                $baseTotalAmountInvoiced = $extrafeeQuote[ExtrafeeQuoteInterface::BASE_FEE_AMOUNT];
                $totalAmountInvoiced = $extrafeeQuote[ExtrafeeQuoteInterface::FEE_AMOUNT];
                $baseTaxAmountInvoiced = $extrafeeQuote[ExtrafeeQuoteInterface::BASE_TAX_AMOUNT];
                $taxAmountInvoiced = $extrafeeQuote[ExtrafeeQuoteInterface::TAX_AMOUNT];

                $table = $setup->getTable(ExtrafeeInvoice::TABLE_NAME);
                $this->insertEntityToExtrafee($setup, $table, $extrafeeQuote, self::INVOICE_ID);
            }

            if (!empty($extrafeeQuote[self::CREDITMEMO_ID])) {
                $baseTotalAmountRefunded = $extrafeeQuote[ExtrafeeQuoteInterface::BASE_FEE_AMOUNT];
                $totalAmountRefunded = $extrafeeQuote[ExtrafeeQuoteInterface::FEE_AMOUNT];
                $baseTaxAmountRefunded = $extrafeeQuote[ExtrafeeQuoteInterface::BASE_TAX_AMOUNT];
                $taxAmountRefunded = $extrafeeQuote[ExtrafeeQuoteInterface::TAX_AMOUNT];
                $isRefunded = true;

                $table = $setup->getTable(ExtrafeeCreditmemo::TABLE_NAME);
                $this->insertEntityToExtrafee($setup, $table, $extrafeeQuote, self::CREDITMEMO_ID);
            }

            $this->insertOrderToExtrafee(
                $setup,
                $extrafeeQuote,
                $baseTotalAmountInvoiced,
                $baseTotalAmountRefunded,
                $totalAmountInvoiced,
                $totalAmountRefunded,
                $baseTaxAmountInvoiced,
                $baseTaxAmountRefunded,
                $taxAmountInvoiced,
                $taxAmountRefunded,
                $isRefunded
            );
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param array $extrafeeQuote
     * @param float $baseTotalAmountInvoiced
     * @param float $baseTotalAmountRefunded
     * @param float $totalAmountInvoiced
     * @param float $totalAmountRefunded
     * @param float $baseTaxAmountInvoiced
     * @param float $baseTaxAmountRefunded
     * @param float $taxAmountInvoiced
     * @param float $taxAmountRefunded
     * @param bool $isRefunded
     */
    private function insertOrderToExtrafee(
        ModuleDataSetupInterface $setup,
        $extrafeeQuote,
        $baseTotalAmountInvoiced,
        $baseTotalAmountRefunded,
        $totalAmountInvoiced,
        $totalAmountRefunded,
        $baseTaxAmountInvoiced,
        $baseTaxAmountRefunded,
        $taxAmountInvoiced,
        $taxAmountRefunded,
        $isRefunded
    ) {
        $setup->getConnection()->insert(
            $setup->getTable(ExtrafeeOrder::TABLE_NAME),
            [
                ExtrafeeOrderInterface::BASE_TOTAL => $extrafeeQuote[ExtrafeeQuoteInterface::BASE_FEE_AMOUNT],
                ExtrafeeOrderInterface::BASE_TOTAL_INVOICED => $baseTotalAmountInvoiced,
                ExtrafeeOrderInterface::BASE_TOTAL_REFUNDED => $baseTotalAmountRefunded,
                ExtrafeeOrderInterface::TOTAL => $extrafeeQuote[ExtrafeeQuoteInterface::FEE_AMOUNT],
                ExtrafeeOrderInterface::TOTAL_INVOICED => $totalAmountInvoiced,
                ExtrafeeOrderInterface::TOTAL_REFUNDED => $totalAmountRefunded,
                ExtrafeeOrderInterface::BASE_TAX => $extrafeeQuote[ExtrafeeQuoteInterface::BASE_TAX_AMOUNT],
                ExtrafeeOrderInterface::BASE_TAX_INVOICED => $baseTaxAmountInvoiced,
                ExtrafeeOrderInterface::BASE_TAX_REFUNDED => $baseTaxAmountRefunded,
                ExtrafeeOrderInterface::TAX => $extrafeeQuote[ExtrafeeQuoteInterface::TAX_AMOUNT],
                ExtrafeeOrderInterface::TAX_INVOICED => $taxAmountInvoiced,
                ExtrafeeOrderInterface::TAX_REFUNDED => $taxAmountRefunded,
                ExtrafeeOrderInterface::LABEL => $extrafeeQuote[self::LABEL],
                ExtrafeeOrderInterface::OPTION_LABEL => $extrafeeQuote[ExtrafeeQuoteInterface::LABEL],
                ExtrafeeOrderInterface::IS_REFUNDED => $isRefunded,
                ExtrafeeOrderInterface::ORDER_ID => $extrafeeQuote[self::ORDER_ID],
                ExtrafeeOrderInterface::FEE_ID => $extrafeeQuote[ExtrafeeQuoteInterface::FEE_ID],
                ExtrafeeOrderInterface::OPTION_ID => $extrafeeQuote[ExtrafeeQuoteInterface::OPTION_ID]
            ]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param string $table
     * @param array $extrafeeQuote
     * @param string $entityIdentifier
     */
    private function insertEntityToExtrafee(ModuleDataSetupInterface $setup, $table, $extrafeeQuote, $entityIdentifier)
    {
        $setup->getConnection()->insert(
            $table,
            [
                'base_total_amount' => $extrafeeQuote[ExtrafeeQuoteInterface::BASE_FEE_AMOUNT],
                'total_amount' => $extrafeeQuote[ExtrafeeQuoteInterface::FEE_AMOUNT],
                'base_tax_amount' => $extrafeeQuote[ExtrafeeQuoteInterface::BASE_TAX_AMOUNT],
                'tax_amount' => $extrafeeQuote[ExtrafeeQuoteInterface::TAX_AMOUNT],
                'fee_label' => $extrafeeQuote[self::LABEL],
                'fee_option_label' => $extrafeeQuote[ExtrafeeQuoteInterface::LABEL],
                'order_id' => $extrafeeQuote[self::ORDER_ID],
                $entityIdentifier => $extrafeeQuote[$entityIdentifier],
                'fee_id' => $extrafeeQuote[ExtrafeeQuoteInterface::FEE_ID],
                'option_id' => $extrafeeQuote[ExtrafeeQuoteInterface::OPTION_ID]
            ]
        );
    }
}
