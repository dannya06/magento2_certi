<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup;

use Amasty\Extrafee\Setup\Operation\AddCalculationColumns;
use Amasty\Extrafee\Setup\Operation\AddIsRequiredAndIsEligibleAndProductConditionsColumns;
use Amasty\Extrafee\Setup\Operation\AddTaxColumns;
use Amasty\Extrafee\Setup\Operation\ChangeIdColumnType;
use Amasty\Extrafee\Setup\Operation\CreateFeeCreditMemoTable;
use Amasty\Extrafee\Setup\Operation\CreateFeeOrderTable;
use Amasty\Extrafee\Setup\Operation\CreateFeeInvoiceTable;
use Amasty\Extrafee\Setup\Operation\RemoveCalculationField;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var AddCalculationColumns
     */
    private $addCalculationColumns;

    /**
     * @var ChangeIdColumnType
     */
    private $changeIdColumnType;

    /**
     * @var AddTaxColumns
     */
    private $addTaxColumns;

    /**
     * @var AddIsRequiredAndIsEligibleAndProductConditionsColumns
     */
    private $addSubsidiaryColumns;

    /**
     * @var RemoveCalculationField
     */
    private $removeCalculationField;

    /**
     * @var CreateFeeOrderTable
     */
    private $createFeeOrderTable;

    /**
     * @var CreateFeeInvoiceTable
     */
    private $createFeeInvoiceTable;

    /**
     * @var CreateFeeCreditMemoTable
     */
    private $createFeeCreditMemoTable;

    public function __construct(
        AddCalculationColumns $addCalculationColumns,
        ChangeIdColumnType $changeIdColumnType,
        AddTaxColumns $addTaxColumns,
        AddIsRequiredAndIsEligibleAndProductConditionsColumns $addSubsidiaryColumns,
        RemoveCalculationField $removeCalculationField,
        CreateFeeOrderTable $createFeeOrderTable,
        CreateFeeInvoiceTable $createFeeInvoiceTable,
        CreateFeeCreditMemoTable $createFeeCreditMemoTable
    ) {
        $this->addCalculationColumns = $addCalculationColumns;
        $this->changeIdColumnType = $changeIdColumnType;
        $this->addTaxColumns = $addTaxColumns;
        $this->addSubsidiaryColumns = $addSubsidiaryColumns;
        $this->removeCalculationField = $removeCalculationField;
        $this->createFeeOrderTable = $createFeeOrderTable;
        $this->createFeeInvoiceTable = $createFeeInvoiceTable;
        $this->createFeeCreditMemoTable = $createFeeCreditMemoTable;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addCalculationColumns->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $this->changeIdColumnType->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.1.4', '<')) {
            $this->addTaxColumns->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->addSubsidiaryColumns->execute($setup);
            $this->removeCalculationField->execute($setup);
            $this->createFeeOrderTable->execute($setup);
            $this->createFeeInvoiceTable->execute($setup);
            $this->createFeeCreditMemoTable->execute($setup);
        }

        $setup->endSetup();
    }
}
