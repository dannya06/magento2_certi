<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Test\Unit\Model\Order\Total\Creditmemo;

use Amasty\Extrafee\Model\ExtrafeeOrder;
use Amasty\Extrafee\Model\Order\Total\Creditmemo\Fee;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\Collection;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\CollectionFactory;
use Amasty\Extrafee\Test\Unit\Traits\ObjectManagerTrait;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Creditmemo;
use PHPUnit\Framework\TestCase;

/**
 * Class FeeTest
 *
 * @see \Amasty\Extrafee\Model\Order\Total\Creditmemo\Fee
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class FeeTest extends TestCase
{
    use ObjectManagerTrait;

    /**
     * @var RequestInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    /**
     * @var Creditmemo|\PHPUnit\Framework\MockObject\MockObject
     */
    private $creditmemoMock;

    /**
     * @var Collection|\PHPUnit\Framework\MockObject\MockObject
     */
    private $feeOrderCollectionMock;

    /**
     * @var Fee
     */
    private $model;

    public function setUp(): void
    {
        $orderMock = $this->createMock(\Magento\Sales\Model\Order::class);
        $this->creditmemoMock = $this->createMock(Creditmemo::class);
        $this->creditmemoMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $this->requestMock = $this->createMock(RequestInterface::class);


        $this->feeOrderCollectionMock = $this->createMock(Collection::class);

        $feeOrderCollectionFactoryMock = $this->createMock(CollectionFactory::class);
        $feeOrderCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->feeOrderCollectionMock);

        $priceCurrencyMock = $this->createMock(PriceCurrencyInterface::class);
        $priceCurrencyMock->expects($this->any())->method('round')
            ->will($this->returnCallback(function ($argument) {
                return round($argument, 2);
            }));

        $this->model = $this->getObjectManager()->getObject(Fee::class, [
            'request' => $this->requestMock,
            'priceCurrency' => $priceCurrencyMock,
            'feeOrderCollectionFactory' => $feeOrderCollectionFactoryMock
        ]);
    }

    /**
     * @covers \Amasty\Extrafee\Model\Order\Total\Creditmemo\Fee::collectFee
     * @dataProvider collectFeeDataProvider
     */
    public function testCollectFee(
        array $requestParams,
        array $feeOrdersData,
        bool $creditmemoIsLast,
        float $creditmemoBaseGrandTotal,
        float $creditmemoGrandTotal,
        float $creditmemoBaseTaxAmount,
        float $creditmemoTaxAmount,
        float $expectedBaseGrandTotal,
        float $expectedGrandTotal,
        float $expectedBaseTaxAmount,
        float $expectedTaxAmount
    ) {
        $feeOrderMocks = [];
        foreach ($feeOrdersData as $feeOrderData) {
            $feeOrderMocks[] = $this->createFeeOrderMock($feeOrderData);
        }

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('creditmemo')
            ->willReturn($requestParams);

        $this->feeOrderCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($feeOrderMocks);

        $this->creditmemoMock->expects($this->once())
            ->method('isLast')
            ->willReturn($creditmemoIsLast);

        $this->creditmemoMock->expects($this->any())
            ->method('getGrandTotal')
            ->willReturn($creditmemoGrandTotal);
        $this->creditmemoMock->expects($this->any())
            ->method('getBaseGrandTotal')
            ->willReturn($creditmemoBaseGrandTotal);
        $this->creditmemoMock->expects($this->any())
            ->method('getTaxAmount')
            ->willReturn($creditmemoTaxAmount);
        $this->creditmemoMock->expects($this->any())
            ->method('getBaseTaxAmount')
            ->willReturn($creditmemoBaseTaxAmount);

        // assert expectations
        $this->creditmemoMock->expects($this->once())
            ->method('setGrandTotal')
            ->with($expectedGrandTotal);
        $this->creditmemoMock->expects($this->once())
            ->method('setBaseGrandTotal')
            ->with($expectedBaseGrandTotal);
        $this->creditmemoMock->expects($this->once())
            ->method('setTaxAmount')
            ->with($expectedTaxAmount);
        $this->creditmemoMock->expects($this->once())
            ->method('setBaseTaxAmount')
            ->with($expectedBaseTaxAmount);

        $this->model->collectFee($this->creditmemoMock);
    }

    /**
     * @return array
     */
    public function collectFeeDataProvider()
    {
        // @TODO: add case with more than one extra fee
        return [
            'zeroFee' => [
                'requestParams' => [],
                'feeOrdersData' => [
                    [
                        'fee_id' => 1,
                        'option_id' => 1,
                        'is_eligible_refund' => 1,
                        'total_amount' => 0.00,
                        'total_amount_refunded' => 0.00,
                        'base_total_amount' => 0.00,
                        'base_total_amount_refunded' => 0.00,
                        'tax_amount' => 0.00,
                        'tax_amount_refunded' => 0.00,
                        'base_tax_amount' => 0.00,
                        'base_tax_amount_refunded' => 0.00,
                    ]
                ],
                'creditmemoIsLast' => true,
                'creditmemo_base_total' => 17.00,
                'creditmemo_total' => 17.00,
                'creditmemo_base_tax' => 2.00,
                'creditmemo_tax' => 2.00,
                'expected_base_grand_total' => 17.00,
                'expected_grand_total' => 17.00,
                'expected_base_tax_amount' => 2.00,
                'expected_tax_amount' => 2.00,
            ],
            'oneDollarFee' => [
                'requestParams' => [],
                'feeOrdersData' => [
                    [
                        'fee_id' => 1,
                        'option_id' => 1,
                        'is_eligible_refund' => 1,
                        'total_amount' => 1.00,
                        'total_amount_refunded' => 0.00,
                        'base_total_amount' => 1.00,
                        'base_total_amount_refunded' => 0.00,
                        'tax_amount' => 0.00,
                        'tax_amount_refunded' => 0.00,
                        'base_tax_amount' => 0.00,
                        'base_tax_amount_refunded' => 0.00,
                    ]
                ],
                'creditmemoIsLast' => true,
                'creditmemo_base_total' => 17.00,
                'creditmemo_total' => 17.00,
                'creditmemo_base_tax' => 2.00,
                'creditmemo_tax' => 2.00,
                'expected_base_grand_total' => 18.00,
                'expected_grand_total' => 18.00,
                'expected_base_tax_amount' => 2.00,
                'expected_tax_amount' => 2.00,
            ],
            'nonRefundableFee1DollarTax' => [
                'requestParams' => [],
                'feeOrdersData' => [
                    [
                        'fee_id' => 1,
                        'option_id' => 1,
                        'is_eligible_refund' => 0,
                        'total_amount' => 1.00,
                        'total_amount_refunded' => 0.00,
                        'base_total_amount' => 1.00,
                        'base_total_amount_refunded' => 0.00,
                        'tax_amount' => 1.00,
                        'tax_amount_refunded' => 0.00,
                        'base_tax_amount' => 1.00,
                        'base_tax_amount_refunded' => 0.00,
                    ]
                ],
                'creditmemoIsLast' => true,
                'creditmemo_base_total' => 17.00,
                'creditmemo_total' => 17.00,
                'creditmemo_base_tax' => 2.00,
                'creditmemo_tax' => 2.00,
                'expected_base_grand_total' => 16.00,
                'expected_grand_total' => 16.00,
                'expected_base_tax_amount' => 1.00,
                'expected_tax_amount' => 1.00,
            ],
            'nonRefundableFee1DollarTaxNotLastCreditmemo' => [
                'requestParams' => [],
                'feeOrdersData' => [
                    [
                        'fee_id' => 1,
                        'option_id' => 1,
                        'is_eligible_refund' => 0,
                        'total_amount' => 1.00,
                        'total_amount_refunded' => 0.00,
                        'base_total_amount' => 1.00,
                        'base_total_amount_refunded' => 0.00,
                        'tax_amount' => 1.00,
                        'tax_amount_refunded' => 0.00,
                        'base_tax_amount' => 1.00,
                        'base_tax_amount_refunded' => 0.00,
                    ]
                ],
                'creditmemoIsLast' => false,
                'creditmemo_base_total' => 17.00,
                'creditmemo_total' => 17.00,
                'creditmemo_base_tax' => 2.00,
                'creditmemo_tax' => 2.00,
                'expected_base_grand_total' => 17.00,
                'expected_grand_total' => 17.00,
                'expected_base_tax_amount' => 2.00,
                'expected_tax_amount' => 2.00,
            ],

            'refundableFeePartialRefund' => [
                'requestParams' => [
                    'extra_fee_1_1' => 5.00
                ],
                'feeOrdersData' => [
                    [
                        'fee_id' => 1,
                        'option_id' => 1,
                        'is_eligible_refund' => 1,
                        'total_amount' => 10.00,
                        'total_amount_refunded' => 0.00,
                        'base_total_amount' => 10.00,
                        'base_total_amount_refunded' => 0.00,
                        'tax_amount' => 1.00,
                        'tax_amount_refunded' => 0.00,
                        'base_tax_amount' => 1.00,
                        'base_tax_amount_refunded' => 0.00,
                    ]
                ],
                'creditmemoIsLast' => true,
                'creditmemo_base_total' => 17.00,
                'creditmemo_total' => 17.00,
                'creditmemo_base_tax' => 2.00,
                'creditmemo_tax' => 2.00,
                'expected_base_grand_total' => 21.50,
                'expected_grand_total' => 21.50,
                'expected_base_tax_amount' => 1.50,
                'expected_tax_amount' => 1.50,
            ],

            'refundableFeeFullRefund' => [
                'requestParams' => [
                    'extra_fee_1_1' => 10.00
                ],
                'feeOrdersData' => [
                    [
                        'fee_id' => 1,
                        'option_id' => 1,
                        'is_eligible_refund' => 1,
                        'total_amount' => 10.00,
                        'total_amount_refunded' => 0.00,
                        'base_total_amount' => 10.00,
                        'base_total_amount_refunded' => 0.00,
                        'tax_amount' => 1.00,
                        'tax_amount_refunded' => 0.00,
                        'base_tax_amount' => 1.00,
                        'base_tax_amount_refunded' => 0.00,
                    ]
                ],
                'creditmemoIsLast' => true,
                'creditmemo_base_total' => 17.00,
                'creditmemo_total' => 17.00,
                'creditmemo_base_tax' => 2.00,
                'creditmemo_tax' => 2.00,
                'expected_base_grand_total' => 27.00,
                'expected_grand_total' => 27.00,
                'expected_base_tax_amount' => 2.00,
                'expected_tax_amount' => 2.00,
            ],
            'refundableFeePartialRefundedFullRefund' => [
                'requestParams' => [
                    'extra_fee_1_1' => 5.00
                ],
                'feeOrdersData' => [
                    [
                        'fee_id' => 1,
                        'option_id' => 1,
                        'is_eligible_refund' => 1,
                        'total_amount' => 10.00,
                        'total_amount_refunded' => 5.00,
                        'base_total_amount' => 10.00,
                        'base_total_amount_refunded' => 5.00,
                        'tax_amount' => 1.00,
                        'tax_amount_refunded' => 0.50,
                        'base_tax_amount' => 1.00,
                        'base_tax_amount_refunded' => 0.50,
                    ]
                ],
                'creditmemoIsLast' => true,
                'creditmemo_base_total' => 0.50,
                'creditmemo_total' => 0.50,
                'creditmemo_base_tax' => 0.50,
                'creditmemo_tax' => 0.50,
                'expected_base_grand_total' => 5.50,
                'expected_grand_total' => 5.50,
                'expected_base_tax_amount' => 0.50,
                'expected_tax_amount' => 0.50,
            ],
        ];
    }

    /**
     * @param array $feeOrderData
     * @return ExtrafeeOrder|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createFeeOrderMock(array $feeOrderData)
    {
        $feeOrderMock = $this->createMock(ExtrafeeOrder::class);

        $feeOrderMock->expects($this->any())
            ->method('getFeeId')
            ->willReturn($feeOrderData['fee_id']);
        $feeOrderMock->expects($this->any())
            ->method('getOptionId')
            ->willReturn($feeOrderData['option_id']);
        $feeOrderMock->expects($this->any())
            ->method('getTotalAmount')
            ->willReturn($feeOrderData['total_amount']);
        $feeOrderMock->expects($this->any())
            ->method('getTotalAmountRefunded')
            ->willReturn($feeOrderData['total_amount_refunded']);
        $feeOrderMock->expects($this->any())
            ->method('getBaseTotalAmount')
            ->willReturn($feeOrderData['base_total_amount']);
        $feeOrderMock->expects($this->any())
            ->method('getBaseTotalAmountRefunded')
            ->willReturn($feeOrderData['base_total_amount_refunded']);
        $feeOrderMock->expects($this->any())
            ->method('getTaxAmount')
            ->willReturn($feeOrderData['tax_amount']);
        $feeOrderMock->expects($this->any())
            ->method('getTaxAmountRefunded')
            ->willReturn($feeOrderData['tax_amount_refunded']);
        $feeOrderMock->expects($this->any())
            ->method('getBaseTaxAmount')
            ->willReturn($feeOrderData['base_tax_amount']);
        $feeOrderMock->expects($this->any())
            ->method('getBaseTaxAmountRefunded')
            ->willReturn($feeOrderData['base_tax_amount_refunded']);
        $feeOrderMock->expects($this->any())
            ->method('getData')
            ->with('is_eligible_refund')
            ->willReturn($feeOrderData['is_eligible_refund']);

        return $feeOrderMock;
    }
}
