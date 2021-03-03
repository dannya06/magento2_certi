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
namespace Aheadworks\StoreCredit\Test\Unit\Model\Service;

use Aheadworks\StoreCredit\Model\Service\StoreCreditCartService;
use Aheadworks\StoreCredit\Api\CustomerStoreCreditManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class Aheadworks\StoreCredit\Test\Unit\Model\Service$StoreCreditCartServiceTest
 */
class StoreCreditCartServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StoreCreditCartService
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CustomerStoreCreditManagementInterface
     */
    private $customerStoreCreditManagementMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CartRepositoryInterface
     */
    private $quoteRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CartInterface
     */
    private $quoteMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->customerStoreCreditManagementMock = $this->getMockBuilder(
            CustomerStoreCreditManagementInterface::class
        )
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerStoreCreditBalance'])
            ->getMockForAbstractClass();

        $this->quoteRepositoryMock = $this->getMockBuilder(CartRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getActive', 'save'])
            ->getMockForAbstractClass();

        $this->quoteMock = $this->getMockBuilder(CartInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getAwUseStoreCredit',
                    'setAwUseStoreCredit',
                    'getItemsCount',
                    'getCustomerId',
                    'getShippingAddress',
                    'collectTotals'
                ]
            )
            ->getMockForAbstractClass();

        $data = [
            'customerStoreCreditService' => $this->customerStoreCreditManagementMock,
            'quoteRepository' => $this->quoteRepositoryMock
        ];

        $this->object = $objectManager->getObject(StoreCreditCartService::class, $data);
    }

    /**
     * Test get method
     */
    public function testGetMethod()
    {
        $cartId = 5;
        $awUseStoreCredit = true;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(1);

        $this->quoteMock->expects($this->once())
            ->method('getAwUseStoreCredit')
            ->willReturn($awUseStoreCredit);

        $this->assertTrue($this->object->get($cartId));
    }

    /**
     * Test get method, throw exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 5 doesn't contain products
     */
    public function testGetMethodException()
    {
        $cartId = 5;
        $awUseStoreCredit = true;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(0);

        $this->quoteMock->expects($this->never())
            ->method('getAwUseStoreCredit')
            ->willReturn($awUseStoreCredit);
        $this->expectException(NoSuchEntityException::class);
        $this->object->get($cartId);
    }

    /**
     * Test set method
     */
    public function testSetMethod()
    {
        $cartId = 10;
        $customerId = 4;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);
        $this->quoteMock->expects($this->exactly(2))
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->customerStoreCreditManagementMock->expects($this->once())
            ->method('getCustomerStoreCreditBalance')
            ->with($customerId)
            ->willReturn(12);

        $shippingAddressMock = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCollectShippingRates'])
            ->getMockForAbstractClass();
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true)
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);
        $this->quoteMock->expects($this->once())
            ->method('setAwUseStoreCredit')
            ->with(true)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->quoteMock)
            ->willReturnSelf();

        $this->assertTrue($this->object->set($cartId));
    }

    /**
     * Test set method if quote not has items
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 10 doesn't contain products
     */
    public function testSetMethodNotQuoteItems()
    {
        $cartId = 10;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(0);
        $this->expectException(NoSuchEntityException::class);
        $this->object->set($cartId);
    }

    /**
     * Test set method if customer id is null
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No Store Credit to be used
     */
    public function testSetMethodNullCustomerId()
    {
        $cartId = 10;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(1);
        $this->quoteMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn(null);
        $this->expectException(NoSuchEntityException::class);
        $this->object->set($cartId);
    }

    /**
     * Test set method if customer has null Store Credit balance
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No Store Credit to be used
     */
    public function testSetMethodNullCustomerStoreCreditBalance()
    {
        $cartId = 10;
        $customerId = 5;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(1);
        $this->quoteMock->expects($this->exactly(2))
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->customerStoreCreditManagementMock->expects($this->once())
            ->method('getCustomerStoreCreditBalance')
            ->with($customerId)
            ->willReturn(0);
        $this->expectException(NoSuchEntityException::class);
        $this->object->set($cartId);
    }

    /**
     * Test set method throw exception at save repository
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not apply Store Credit
     */
    public function testSetMethodThrowExceptionAtSaveRepository()
    {
        $cartId = 10;
        $customerId = 4;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);
        $this->quoteMock->expects($this->exactly(2))
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->customerStoreCreditManagementMock->expects($this->once())
            ->method('getCustomerStoreCreditBalance')
            ->with($customerId)
            ->willReturn(12);

        $shippingAddressMock = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCollectShippingRates'])
            ->getMockForAbstractClass();
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true)
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);
        $this->quoteMock->expects($this->once())
            ->method('setAwUseStoreCredit')
            ->with(true)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->quoteMock)
            ->willThrowException(new \Exception('Could not apply Store Credit'));
        $this->expectException(CouldNotSaveException::class);
        $this->object->set($cartId);
    }

    /**
     * Test remove method
     */
    public function testRemoveMethod()
    {
        $cartId = 9;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);

        $shippingAddressMock = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCollectShippingRates'])
            ->getMockForAbstractClass();
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true)
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);
        $this->quoteMock->expects($this->once())
            ->method('setAwUseStoreCredit')
            ->with(false)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->quoteMock)
            ->willReturnSelf();

        $this->assertTrue($this->object->remove($cartId));
    }

    /**
     * Test remove method if quote not has items
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 21 doesn't contain products
     */
    public function testRemoveMethodNotQuoteItems()
    {
        $cartId = 21;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(0);
        $this->expectException(NoSuchEntityException::class);
        $this->object->remove($cartId);
    }

    /**
     * Test remove method throw exception at save repository
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Could not remove Store Credit
     */
    public function testRemoveMethodThrowExceptionAtSaveRepository()
    {
        $cartId = 12;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);

        $shippingAddressMock = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCollectShippingRates'])
            ->getMockForAbstractClass();
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true)
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);
        $this->quoteMock->expects($this->once())
            ->method('setAwUseStoreCredit')
            ->with(false)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->quoteMock)
            ->willThrowException(new \Exception('Could not remove Store Credit'));
        $this->expectException(CouldNotDeleteException::class);
        $this->object->remove($cartId);
    }
}
