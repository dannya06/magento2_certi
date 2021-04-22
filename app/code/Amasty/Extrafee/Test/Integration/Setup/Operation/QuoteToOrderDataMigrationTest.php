<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Test\Integration\Setup\Operation;

use Amasty\Extrafee\Model\ResourceModel\ExtrafeeCreditmemo;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeInvoice;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote;
use Amasty\Extrafee\Model\ResourceModel\Fee;
use Amasty\Extrafee\Setup\Operation\QuoteToOrderDataMigration;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class QuoteToOrderDataMigrationTest
 *
 * @see QuoteToOrderDataMigration
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class QuoteToOrderDataMigrationTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var Address
     */
    private $billingAddress;

    /**
     * @var Address
     */
    private $shippingAddress;

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var int
     */
    private $storeId = 0;
    private $quoteId = 0;
    private $orderId = 0;
    private $invoiceId = 0;
    private $creditmemoId = 0;
    private $incrementId = 0;

    /**
     * @var string
     */
    private $feeOrderTableName;
    private $feeInvoiceTableName;
    private $feeCreditmemoTableName;
    private $feeTableName;
    private $feeOptionTableName;
    private $feeQuoteTableName;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        /** @var ModuleDataSetupInterface $moduleDataSetup */
        $this->moduleDataSetup = $this->objectManager->get(ModuleDataSetupInterface::class);
        $this->storeId = $this->objectManager->get(StoreManagerInterface::class)->getStore()->getId();

        $this->connection = $this->moduleDataSetup->getConnection();
        $this->feeOrderTableName = $this->moduleDataSetup->getTable(ExtrafeeOrder::TABLE_NAME);
        $this->feeInvoiceTableName = $this->moduleDataSetup->getTable(ExtrafeeInvoice::TABLE_NAME);
        $this->feeCreditmemoTableName = $this->moduleDataSetup->getTable(ExtrafeeCreditmemo::TABLE_NAME);
        $this->feeQuoteTableName = $this->moduleDataSetup->getTable(ExtrafeeQuote::TABLE_NAME);
        $this->feeTableName = $this->moduleDataSetup->getTable(Fee::TABLE_NAME);
        $this->feeOptionTableName = $this->moduleDataSetup->getTable('amasty_extrafee_option');
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $feeValues
     * @param array $feeOptionValues
     * @param array $feeQuoteValues
     * @param bool $isCreateOrder
     * @param bool $isCreateInvoice
     * @param bool $isCreateCreditmemo
     * @param int $expectedFeeOrderCount
     * @param int $expectedFeeInvoiceCount
     * @param int $expectedFeeCreditMemoCount
     * @throws \Exception
     */
    public function testExecute(
        $feeValues,
        $feeOptionValues,
        $feeQuoteValues,
        $isCreateOrder,
        $isCreateInvoice,
        $isCreateCreditmemo,
        $expectedFeeOrderCount,
        $expectedFeeInvoiceCount,
        $expectedFeeCreditMemoCount
    ) {
        $this->prepareData($isCreateOrder, $isCreateInvoice, $isCreateCreditmemo);

        if ($this->quoteId) {
            $feeQuoteValues['quote_id'] = $this->quoteId;
        }

        $this->saveValues($feeValues, $this->feeTableName);
        $this->saveValues($feeOptionValues, $this->feeOptionTableName);
        $this->saveValues($feeQuoteValues, $this->feeQuoteTableName);

        /** @var QuoteToOrderDataMigration $quoteToOrderDataMigration */
        $quoteToOrderDataMigration = $this->objectManager->get(QuoteToOrderDataMigration::class);
        $quoteToOrderDataMigration->execute($this->moduleDataSetup);

        $this->assertEquals($expectedFeeOrderCount, $this->getCountFromDb($this->feeOrderTableName));
        $this->assertEquals($expectedFeeInvoiceCount, $this->getCountFromDb($this->feeInvoiceTableName));
        $this->assertEquals($expectedFeeCreditMemoCount, $this->getCountFromDb($this->feeCreditmemoTableName));

        $this->deleteData($isCreateOrder, $isCreateInvoice, $isCreateCreditmemo);
    }

    /**
     * @param array $tableRows
     * @param string $tableName
     */
    private function saveValues($tableRows, $tableName)
    {
        if (empty($tableRows)) {
            return;
        }
        $this->connection->insertMultiple(
            $tableName,
            $tableRows
        );
    }

    /**
     * @param bool $isCreateOrder
     * @param bool $isCreateInvoice
     * @param bool $isCreateCreditmemo
     * @throws \Exception
     */
    private function prepareData($isCreateOrder, $isCreateInvoice, $isCreateCreditmemo)
    {
        if ($isCreateOrder) {
            $this->createAddresses();
            $this->createPayment();
            $this->createQuote();
            $this->createOrder();
        }

        if ($isCreateInvoice && $this->orderId) {
            $this->createInvoice();
        }

        if ($isCreateCreditmemo && $this->orderId) {
            $this->createCreditmemo();
        }
    }

    private function createAddresses()
    {
        $addressData = [
            'region' => 'CA',
            'region_id' => '12',
            'postcode' => '11111',
            'lastname' => 'lastname',
            'firstname' => 'firstname',
            'street' => 'street',
            'city' => 'Los Angeles',
            'email' => 'admin@example.com',
            'telephone' => '11111111',
            'country_id' => 'US'
        ];

        $billingAddress = $this->objectManager->create(Address::class, ['data' => $addressData]);
        $billingAddress->setAddressType('billing');
        $this->billingAddress = $billingAddress;

        $shippingAddress = clone $billingAddress;
        $shippingAddress->setId(null)->setAddressType('shipping');
        $this->shippingAddress = $shippingAddress;
    }

    private function createPayment()
    {
        $payment = $this->objectManager->create(Payment::class);
        $payment->setMethod('checkmo');
        $payment->setAdditionalInformation('last_trans_id', '11122');
        $payment->setAdditionalInformation('metadata', [
            'type' => 'free',
            'fraudulent' => false
        ]);

        $this->payment = $payment;
    }

    private function createQuote()
    {
        $this->cartManagement = $this->objectManager->create(CartManagementInterface::class);
        $this->quoteId = $this->cartManagement->createEmptyCart();
    }

    /**
     * @throws \Exception
     */
    private function createOrder()
    {
        /** @var $order Order */
        $orderCollection = $this->objectManager->create(Collection::class);
        $this->incrementId = $orderCollection->getLastItem()->getIncrementId() + 1;

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);

        $order->setIncrementId(
            $this->incrementId
        )->setState(
            Order::STATE_PROCESSING
        )->setStatus(
            $order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING)
        )->setSubtotal(
            100
        )->setGrandTotal(
            100
        )->setBaseSubtotal(
            100
        )->setBaseGrandTotal(
            100
        )->setCustomerIsGuest(
            true
        )->setCustomerId(
            null
        )->setCustomerEmail(
            'unknown@example.com'
        )->setBillingAddress(
            $this->billingAddress
        )->setShippingAddress(
            $this->shippingAddress
        )->setStoreId(
            $this->storeId
        )->setPayment(
            $this->payment
        );

        $order->isObjectNew(true);
        $order->setQuoteId($this->quoteId);

        $order->save();

        $this->orderId = $order->getId();
    }

    private function createInvoice()
    {
        /** @var Invoice $invoice */
        $invoice = $this->objectManager->create(Invoice::class);
        $invoice->setOrderId($this->orderId);
        $invoice->save();

        $this->invoiceId = $invoice->getId();
    }

    private function createCreditmemo()
    {
        /** @var Creditmemo $creditmemoId */
        $creditmemo = $this->objectManager->create(Creditmemo::class);
        $creditmemo->setOrderId($this->orderId);
        $creditmemo->save();

        $this->creditmemoId = $creditmemo->getId();
    }

    /**
     * @param bool $isDeleteOrder
     * @param bool $isDeleteInvoice
     * @param bool $isDeleteCreditmemo
     * @throws \Exception
     */
    private function deleteData($isDeleteOrder, $isDeleteInvoice, $isDeleteCreditmemo)
    {
        if ($isDeleteOrder) {
            $this->deleteQuote();
            $this->deleteOrder();
            $this->clearTable($this->feeTableName);
            $this->clearTable($this->feeOptionTableName);
            $this->clearTable($this->feeQuoteTableName);
            $this->clearTable($this->feeOrderTableName);
        }

        if ($isDeleteInvoice && $this->orderId) {
            $this->deleteInvoice($this->orderId);
            $this->clearTable($this->feeInvoiceTableName);
        }

        if ($isDeleteCreditmemo && $this->orderId) {
            $this->deleteCreditmemo($this->orderId);
            $this->clearTable($this->feeCreditmemoTableName);
        }
    }

    private function deleteQuote()
    {
        /** @var $quote Quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load($this->quoteId);
        if ($quote->getId()) {
            $quote->delete();
        }
    }

    private function deleteOrder()
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $orderCollection = $this->objectManager->create(Collection::class)
            ->addFieldToFilter('increment_id', $this->incrementId);

        foreach ($orderCollection as $order) {
            $order->delete();
        }

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }

    /**
     * @param int $orderId
     */
    private function deleteInvoice($orderId)
    {
        $this->connection->delete(
            $this->moduleDataSetup->getTable('sales_invoice'),
            [
                'order_id = ?' => $orderId
            ]
        );
    }

    /**
     * @param int $orderId
     */
    private function deleteCreditmemo($orderId)
    {
        $this->connection->delete(
            $this->moduleDataSetup->getTable('sales_creditmemo'),
            [
                'order_id = ?' => $orderId
            ]
        );
    }

    /**
     * @param string $tableName
     */
    private function clearTable($tableName)
    {
        $this->connection->delete($tableName);
    }

    /**
     * @param string $tableName
     * @return int
     */
    private function getCountFromDb($tableName)
    {
        $select = $this->connection
            ->select()
            ->from($tableName, [new \Zend_Db_Expr('COUNT(*)')])
            ->where('order_id = ?', $this->orderId);

        return (int)$this->connection->fetchOne($select);
    }


    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            'emptyAllData' => [
                [],
                [],
                [],
                false,
                false,
                false,
                0,
                0,
                0
            ],
            'customDataWithOrder' => [
                [
                    'entity_id' => 1,
                    'enabled' => 1,
                    'name' => 'Extra Fee',
                    'frontend_type' => 'dropdown',
                    'is_per_product' => 0
                ],
                [
                    'entity_id' => 1,
                    'fee_id' => 1,
                    'price' => 10.0000,
                    'order' => 1,
                    'price_type' => 'fixed',
                    'default' => 1,
                    'admin' => 'Fee Option',
                    'options_serialized' => ''
                ],
                [
                    'entity_id' => 1,
                    'fee_id' => 1,
                    'option_id' => 1,
                    'fee_amount' => 10.0000,
                    'base_fee_amount' => 10.0000,
                    'label' => 'Extra Fee',
                    'tax_amount' => 1.0000,
                    'base_tax_amount' => 1.0000,
                ],
                true,
                false,
                false,
                1,
                0,
                0
            ],
            'customDataWithInvoice' => [
                [
                    'entity_id' => 1,
                    'enabled' => 1,
                    'name' => 'Extra Fee',
                    'frontend_type' => 'dropdown',
                    'is_per_product' => 0
                ],
                [
                    'entity_id' => 1,
                    'fee_id' => 1,
                    'price' => 20.0000,
                    'order' => 1,
                    'price_type' => 'fixed',
                    'default' => 1,
                    'admin' => 'Fee Option',
                    'options_serialized' => ''
                ],
                [
                    'entity_id' => 1,
                    'fee_id' => 1,
                    'option_id' => 1,
                    'fee_amount' => 20.0000,
                    'base_fee_amount' => 20.0000,
                    'label' => 'Extra Fee',
                    'tax_amount' => 2.0000,
                    'base_tax_amount' => 2.0000,
                ],
                true,
                true,
                false,
                1,
                1,
                0
            ],
            'customDataWithCreditMemo' => [
                [
                    'entity_id' => 1,
                    'enabled' => 1,
                    'name' => 'Extra Fee',
                    'frontend_type' => 'dropdown',
                    'is_per_product' => 0
                ],
                [
                    'entity_id' => 1,
                    'fee_id' => 1,
                    'price' => 30.0000,
                    'order' => 1,
                    'price_type' => 'fixed',
                    'default' => 1,
                    'admin' => 'Fee Option',
                    'options_serialized' => ''
                ],
                [
                    'entity_id' => 1,
                    'fee_id' => 1,
                    'option_id' => 1,
                    'fee_amount' => 30.0000,
                    'base_fee_amount' => 30.0000,
                    'label' => 'Extra Fee',
                    'tax_amount' => 3.0000,
                    'base_tax_amount' => 3.0000,
                ],
                true,
                false,
                true,
                1,
                0,
                1
            ],
            'customDataWithAllData' => [
                [
                    'entity_id' => 1,
                    'enabled' => 1,
                    'name' => 'Extra Fee',
                    'frontend_type' => 'dropdown',
                    'is_per_product' => 0
                ],
                [
                    'entity_id' => 1,
                    'fee_id' => 1,
                    'price' => 40.0000,
                    'order' => 1,
                    'price_type' => 'fixed',
                    'default' => 1,
                    'admin' => 'Fee Option',
                    'options_serialized' => ''
                ],
                [
                    'entity_id' => 1,
                    'fee_id' => 1,
                    'option_id' => 1,
                    'fee_amount' => 40.0000,
                    'base_fee_amount' => 40.0000,
                    'label' => 'Extra Fee',
                    'tax_amount' => 4.0000,
                    'base_tax_amount' => 4.0000,
                ],
                true,
                true,
                true,
                1,
                1,
                1
            ]
        ];
    }
}
