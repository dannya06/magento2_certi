<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Setup;

use Amasty\Extrafee\Setup\Operation\CreateExtrafeeTable;
use Amasty\Extrafee\Setup\Operation\CreateFeeCustomerGroupsTable;
use Amasty\Extrafee\Setup\Operation\CreateFeeOptionsTable;
use Amasty\Extrafee\Setup\Operation\CreateFeeQuoteTable;
use Amasty\Extrafee\Setup\Operation\CreateFeeStoresTable;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var CreateExtrafeeTable
     */
    private $createExtrafeeTable;

    /**
     * @var CreateFeeOptionsTable
     */
    private $createFeeOptionsTable;

    /**
     * @var CreateFeeStoresTable
     */
    private $createFeeStoresTable;

    /**
     * @var CreateFeeCustomerGroupsTable
     */
    private $createFeeCustomerGroupsTable;

    /**
     * @var CreateFeeQuoteTable
     */
    private $createFeeQuoteTable;

    public function __construct(
        CreateExtrafeeTable $createExtrafeeTable,
        CreateFeeOptionsTable $createFeeOptionsTable,
        CreateFeeStoresTable $createFeeStoresTable,
        CreateFeeCustomerGroupsTable $createFeeCustomerGroupsTable,
        CreateFeeQuoteTable $createFeeQuoteTable
    ) {
        $this->createExtrafeeTable = $createExtrafeeTable;
        $this->createFeeOptionsTable = $createFeeOptionsTable;
        $this->createFeeStoresTable = $createFeeStoresTable;
        $this->createFeeCustomerGroupsTable = $createFeeCustomerGroupsTable;
        $this->createFeeQuoteTable = $createFeeQuoteTable;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->createExtrafeeTable->execute($installer);
        $this->createFeeOptionsTable->execute($installer);
        $this->createFeeStoresTable->execute($installer);
        $this->createFeeCustomerGroupsTable->execute($installer);
        $this->createFeeQuoteTable->execute($installer);

        $installer->endSetup();
    }
}
