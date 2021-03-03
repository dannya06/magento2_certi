<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    StoreCredit
 * @version    1.1.7
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\StoreCredit\Test\Unit\Model\Comment;

use Aheadworks\StoreCredit\Model\Comment\CommentPool;
use Aheadworks\StoreCredit\Model\Comment\CommentInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\StoreCredit\Model\Source\TransactionType;

/**
 * Class Aheadworks\StoreCredit\Test\Unit\Model\Comment\CommentPoolTest
 */
class CommentPoolTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CommentPool
     */
    private $object;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectManagerInterface
     */
    private $objectManagerMock;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $comments = [];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $this->data = [
            'objectManager' => $this->objectManagerMock,
        ];

        $this->comments = [
            'default' => CommentInterface::class,
            'comment_for_purchases' => CommentInterface::class,
        ];
    }

    /**
     * Init object
     */
    private function initCommentPool()
    {
        $this->data['comments'] = $this->comments;
        $this->object = $this->objectManager->getObject(CommentPool::class, $this->data);
    }

    /**
     * Tests construct for logic exception
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Default comment should be provided.
     */
    public function testConstructLogicException()
    {
        $this->data['comments'] = [];
        $this->expectException(\LogicException::class);
        $this->objectManager->getObject(CommentPool::class, $this->data);
    }

    /**
     * Test construct
     */
    public function testConstruct()
    {
        $this->initCommentPool();
        $ref = new \ReflectionClass($this->object);

        $propComments = $ref->getProperty('comments');
        $propComments->setAccessible(true);
        $valueComments = $propComments->getValue($this->object);
        $propComments->setAccessible(false);

        $propObjectManager = $ref->getProperty('objectManager');
        $propObjectManager->setAccessible(true);
        $valueObjectManager = $propObjectManager->getValue($this->object);
        $propObjectManager->setAccessible(false);

        $this->assertTrue($valueComments == $this->comments);
        $this->assertTrue($valueObjectManager == $this->objectManagerMock);
    }

    /**
     * Tests get method, retrieve comment for purchases instance
     */
    public function testGetMethodRetrievCommentForPurchasesInstance()
    {
        $this->initCommentPool();

        $commentInstanceMock = $this->getMockForAbstractClass(
            CommentInterface::class,
            ['getType'],
            '',
            false
        );
        $commentInstanceMock->expects($this->exactly(2))
            ->method('getType')
            ->willReturn(TransactionType::STORE_CREDIT_USED_IN_ORDER);

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with(CommentInterface::class)
            ->willReturn($commentInstanceMock);

        $this->assertSame($commentInstanceMock, $this->object->get(TransactionType::STORE_CREDIT_USED_IN_ORDER));
        //test cache instance
        $this->assertSame($commentInstanceMock, $this->object->get(TransactionType::STORE_CREDIT_USED_IN_ORDER));
    }

    /**
     * Tests get method, retrieve comment for specific comment
     */
    public function testGetMethodRetrievCommentForPurchasesSpecificComment()
    {
        $this->initCommentPool();

        $commentDefaultInstanceMock = $this->getMockForAbstractClass(
            CommentInterface::class,
            [],
            '',
            false
        );

        $commentForPurchaseInstanceMock = $this->getMockForAbstractClass(
            CommentInterface::class,
            ['getType'],
            '',
            false
        );
        $commentForPurchaseInstanceMock->expects($this->once())
            ->method('getType')
            ->willReturn(TransactionType::STORE_CREDIT_USED_IN_ORDER);

        $this->objectManagerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [CommentInterface::class],
                [CommentInterface::class]
            )
            ->willReturnOnConsecutiveCalls(
                $commentDefaultInstanceMock,
                $commentForPurchaseInstanceMock
            );

        $this->assertSame(
            $commentForPurchaseInstanceMock,
            $this->object->get(TransactionType::STORE_CREDIT_USED_IN_ORDER)
        );
    }

    /**
     * Tests get method, retrieve default instance
     */
    public function testGetMethodRetrievDefaultInsatnce()
    {
        $this->initCommentPool();

        $commentDefaultInstanceMock = $this->createMock(CommentInterface::class);
        $commentForPurchaseInstanceMock = $this->createMock(CommentInterface::class);

        $this->objectManagerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [CommentInterface::class],
                [CommentInterface::class]
            )
            ->willReturnOnConsecutiveCalls(
                $commentDefaultInstanceMock,
                $commentForPurchaseInstanceMock
            );

        $this->assertSame($commentDefaultInstanceMock, $this->object->get('test_comment'));
    }
}
