<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Block\Adminhtml\View;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Block\Adminhtml\View\Store;
use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Filter\Store as StoreFilter;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Adminhtml\View\Store
 */
class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Store
     */
    private $block;

    /**
     * @var StoreFilter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeFilterMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->storeFilterMock = $this->getMock(
            StoreFilter::class,
            ['getItems', 'getCurrentItemKey', 'getStoreIds'],
            [],
            '',
            false
        );
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            false,
            false,
            ['getControllerName']
        );

        $contextMock = $objectManager->getObject(Context::class, ['request' => $this->requestMock]);
        $this->block = $objectManager->getObject(
            Store::class,
            [
                'context' => $contextMock,
                'storeFilter' => $this->storeFilterMock,
            ]
        );
    }

    /**
     * Testing of getItems method
     */
    public function testGetItems()
    {
        $this->storeFilterMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $this->requestMock->expects($this->once())
            ->method('getControllerName')
            ->willReturn('controllername');

        $this->assertTrue(is_array($this->block->getItems()));
    }

    /**
     * Testing of getCurrentItemKey method
     */
    public function testGetCurrentItemKey()
    {
        $type = StoreFilter::DEFAULT_TYPE;

        $this->storeFilterMock->expects($this->once())
            ->method('getCurrentItemKey')
            ->willReturn($type);

        $this->assertEquals($type, $this->block->getCurrentItemKey());
    }

    /**
     * Testing of getStoreIds method
     */
    public function testGetStoreIds()
    {
        $this->storeFilterMock->expects($this->once())
            ->method('getStoreIds')
            ->willReturn([]);

        $this->assertTrue(is_array($this->block->getStoreIds()));
    }

    /**
     * Testing of getCurrentItemTitle method
     */
    public function testGetCurrentItemTitle()
    {
        $items[StoreFilter::DEFAULT_TYPE] = [
            'type' => StoreFilter::DEFAULT_TYPE,
            'title' => 'All Store Views'
        ];
        $type = StoreFilter::DEFAULT_TYPE;

        $this->storeFilterMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
        $this->storeFilterMock->expects($this->once())
            ->method('getCurrentItemKey')
            ->willReturn($type);

        $this->assertEquals($items[StoreFilter::DEFAULT_TYPE]['title'], $this->block->getCurrentItemTitle());
    }
}
