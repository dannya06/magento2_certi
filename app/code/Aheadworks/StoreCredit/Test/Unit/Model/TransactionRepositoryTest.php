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
namespace Aheadworks\StoreCredit\Test\Unit\Model;

use Aheadworks\StoreCredit\Model\TransactionRepository;
use Aheadworks\StoreCredit\Model\ResourceModel\Transaction as TransactionResource;
use Aheadworks\StoreCredit\Api\Data\TransactionInterface;
use Aheadworks\StoreCredit\Api\Data\TransactionInterfaceFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class Aheadworks\StoreCredit\Test\Unit\Model\TransactionRepositoryTest
 */
class TransactionRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TransactionRepository
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TransactionResource
     */
    private $resourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManager
     */
    private $entityManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TransactionInterfaceFactory
     */
    private $modelFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TransactionInterface
     */
    private $dataModelMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resourceMock = $this->getMockBuilder(TransactionResource::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'beginTransaction',
                    'commit',
                    'rollBack',
                    'save',
                ]
            )
            ->getMock();

        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(
                ['load', 'save', 'delete']
            )
            ->getMock();

        $this->modelFactoryMock = $this->getMockBuilder(TransactionInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->dataModelMock = $this->getMockBuilder(TransactionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTransactionId'])
            ->getMockForAbstractClass();

        $data = [
            'resource' => $this->resourceMock,
            'entityManager' => $this->entityManagerMock,
            'transactionFactory' => $this->modelFactoryMock,
        ];

        $this->object = $objectManager->getObject(TransactionRepository::class, $data);
    }

    /**
     * Test save method
     */
    public function testSaveMethod()
    {
        $this->dataModelMock->expects($this->once())
            ->method('getTransactionId')
            ->willReturn(5);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($this->dataModelMock)
            ->willReturnSelf();

        $actual = $this->object->save($this->dataModelMock);

        $this->assertEquals($this->dataModelMock, $actual);
    }

    /**
     * Test save method throw exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Unable save transaction
     */
    public function testSaveMethodThrowException()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($this->dataModelMock)
            ->willThrowException(new \Exception('Unable save transaction'));
        $this->expectException(CouldNotSaveException::class);
        $this->object->save($this->dataModelMock);
    }

    /**
     * Test getById method
     */
    public function testGetByIdMethod()
    {
        $this->dataModelMock->expects($this->exactly(2))
            ->method('getTransactionId')
            ->willReturn(1);

        $this->modelFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->dataModelMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($this->dataModelMock, 1)
            ->willReturnSelf();

        $actual = $this->object->getById(1);

        $this->assertEquals($actual, $this->object->getById(1));
    }

    /**
     * Test getById method throw exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Requested transaction doesn't exist
     */
    public function testGetByIdMethodThrowException()
    {
        $this->dataModelMock->expects($this->once())
            ->method('getTransactionId')
            ->willReturn(null);

        $this->modelFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->dataModelMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($this->dataModelMock, 1)
            ->willReturnSelf();
        $this->expectException(NoSuchEntityException::class);
        $actual = $this->object->getById(1);

        $this->assertEquals($actual, $this->object->getById(1));
    }
}
