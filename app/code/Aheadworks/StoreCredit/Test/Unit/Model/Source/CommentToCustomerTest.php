<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\StoreCredit\Test\Unit\Model\Source;

use Aheadworks\StoreCredit\Model\Comment\CommentPool;
use Aheadworks\StoreCredit\Model\Source\CommentToCustomer;
use Aheadworks\StoreCredit\Model\Comment\CommentDefault;
use Aheadworks\StoreCredit\Model\Comment\CommentForOrder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class Aheadworks\StoreCredit\Test\Unit\Model\Source\CommentToCustomerTest
 */
class CommentToCustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommentToCustomer
     */
    private $object;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CommentPool
     */
    private $commentPoolMock;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->commentPoolMock = $this->getMockBuilder(CommentPool::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getAllComments',
            ])
            ->getMockForAbstractClass();

        $data = [
            'commentPool' => $this->commentPoolMock,
        ];

        $this->object = $this->objectManager->getObject(CommentToCustomer::class, $data);
    }

    /**
     * Test toOptionArray method
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testToOptionArrayMethod()
    {
        $commentSpentForOrder = $this->getMock(
            CommentForOrder::class,
            ['getLabel', 'getComment'],
            [
                'comment' => 'spent_for_order',
                'label' => 'Spent Store Credit on order',
            ],
            '',
            false
        );
        $commentSpentForOrder->expects($this->once())
            ->method('getLabel')
            ->willReturn('Spent Store Credit on order');
        $commentSpentForOrder->expects($this->once())
            ->method('getComment')
            ->willReturn('spent_for_order');

        $commentRefundToStoreCredit = $this->getMock(
            CommentDefault::class,
            ['getLabel', 'getComment'],
            [
                'comment' => 'refund_to_store_credit',
                'label' => 'Refund to Store Credit from order',
            ],
            '',
            false
        );
        $commentRefundToStoreCredit->expects($this->once())
            ->method('getLabel')
            ->willReturn('Refund to Store Credit from order');
        $commentRefundToStoreCredit->expects($this->once())
            ->method('getComment')
            ->willReturn('refund_to_store_credit');

        $commentReimbursedSpentStoreCredit = $this->getMock(
            CommentDefault::class,
            ['getLabel', 'getComment'],
            [
                'comment' => 'reimbursed_spent_store_credit',
                'label' => 'Reimbursed spent Store Credit from order',
            ],
            '',
            false
        );
        $commentReimbursedSpentStoreCredit->expects($this->once())
            ->method('getLabel')
            ->willReturn('Reimbursed spent Store Credit from order');
        $commentReimbursedSpentStoreCredit->expects($this->once())
            ->method('getComment')
            ->willReturn('reimbursed_spent_store_credit');

        $commentReimbursedSpentStoreCreditOnOrderCancel = $this->getMock(
            CommentForOrder::class,
            ['getLabel', 'getComment'],
            [
                'comment' => 'reimbursed_spent_sс_on_order_cancel',
                'label' => 'Reimbursed spent Store Credit from cancel order',
            ],
            '',
            false
        );
        $commentReimbursedSpentStoreCreditOnOrderCancel->expects($this->once())
            ->method('getLabel')
            ->willReturn('Reimbursed spent Store Credit from cancel order');
        $commentReimbursedSpentStoreCreditOnOrderCancel->expects($this->once())
            ->method('getComment')
            ->willReturn('reimbursed_spent_sс_on_order_cancel');

        $allComments = [
            'comment_spend_on_checkout' => $commentSpentForOrder,
            'comment_refund_to_store_credit' => $commentRefundToStoreCredit,
            'comment_reimbursed_spent_store_credit' => $commentReimbursedSpentStoreCredit,
            'comment_reimbursed_spent_sс_on_order_cancel' => $commentReimbursedSpentStoreCreditOnOrderCancel
        ];

        $expectedValue = [
            [
                'value' => 'spent_for_order',
                'label' => 'Spent Store Credit on order',
            ],
            [
                'value' => 'refund_to_store_credit',
                'label' => 'Refund to Store Credit from order',
            ],
            [
                'value' => 'reimbursed_spent_store_credit',
                'label' => 'Reimbursed spent Store Credit from order',
            ],
            [
                'value' => 'reimbursed_spent_sс_on_order_cancel',
                'label' => 'Reimbursed spent Store Credit from cancel order',
            ]
        ];

        $this->commentPoolMock->expects($this->once())
            ->method('getAllComments')
            ->willReturn($allComments);

        $this->assertEquals($expectedValue, $this->object->toOptionArray());
    }
}
