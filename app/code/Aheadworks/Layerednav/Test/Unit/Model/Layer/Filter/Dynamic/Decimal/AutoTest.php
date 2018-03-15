<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Dynamic\Decimal;

use Aheadworks\Layerednav\Model\Layer\Filter\Dynamic\Decimal\Auto;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter\Decimal as ResourceDecimal;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Filter\Price\Range;
use Magento\Catalog\Model\Layer\Filter\Price\Render;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Dynamic\Decimal\Auto
 */
class AutoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Auto
     */
    private $algorithm;

    /**
     * @var ResourceDecimal|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var Render|\PHPUnit_Framework_MockObject_MockObject
     */
    private $renderMock;

    /**
     * @var Range|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rangeMock;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resourceMock = $this->getMockBuilder(ResourceDecimal::class)
            ->setMethods(['getCount', 'getParentCount', 'getMaxValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->renderMock = $this->getMockBuilder(Render::class)
            ->setMethods(['renderRangeData'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->rangeMock = $this->getMockBuilder(Range::class)
            ->setMethods(['getPriceRange'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->algorithm = $objectManager->getObject(
            Auto::class,
            [
                'render' => $this->renderMock,
                'range' => $this->rangeMock,
                'resource' => $this->resourceMock
            ]
        );
    }

    public function testGetItemsData()
    {
        $maxValue = 10.00;
        $count = [1 => '1', 2 => '2'];
        $range = 10;
        $itemsData = [
            [
                'label' => 'Item Label',
                'value' => 'value',
                'count' => 'count'
            ]
        ];

        $this->resourceMock->expects($this->once())
            ->method('getCount')
            ->willReturn($count);
        $this->resourceMock->expects($this->once())
            ->method('getParentCount')
            ->willReturn($count);
        $this->resourceMock->expects($this->once())
            ->method('getMaxValue')
            ->willReturn($maxValue);
        $this->renderMock->expects($this->once())
            ->method('renderRangeData')
            ->with($range, $count)
            ->willReturn($itemsData);

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();

        $this->algorithm->setFilter($filterMock);

        $this->assertEquals($itemsData, $this->algorithm->getItemsData());
    }
}
