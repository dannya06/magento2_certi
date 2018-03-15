<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Dynamic;

use Aheadworks\Layerednav\Model\Layer\Filter\Dynamic\Auto;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter\Price as ResourcePrice;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Price\Range;
use Magento\Catalog\Model\Layer\Filter\Price\Render;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Dynamic\Auto
 */
class AutoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Auto
     */
    private $algorithm;

    /**
     * @var ResourcePrice|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var Layer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerMock;

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

        $this->resourceMock = $this->getMockBuilder(ResourcePrice::class)
            ->setMethods(['getCount', 'getParentCount'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods(['getProductCollection'])
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
        $layerResolverMock = $this->getMockBuilder(LayerResolver::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $layerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($this->layerMock);

        $this->algorithm = $objectManager->getObject(
            Auto::class,
            [
                'layerResolver' => $layerResolverMock,
                'render' => $this->renderMock,
                'range' => $this->rangeMock,
                'resource' => $this->resourceMock
            ]
        );
    }

    public function testGetItemsData()
    {
        $maxPrice = 10.00;
        $count = [1 => '1', 2 => '2'];
        $range = 10;
        $itemsData = [
            [
                'label' => 'Item Label',
                'value' => 'value',
                'count' => 'count'
            ]
        ];

        $productCollectionMock = $this->getMockBuilder(ProductCollection::class)
            ->setMethods(['getMaxPrice'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->layerMock->expects($this->once())
            ->method('getProductCollection')
            ->willReturn($productCollectionMock);
        $productCollectionMock->expects($this->once())
            ->method('getMaxPrice')
            ->willReturn($maxPrice);
        $this->resourceMock->expects($this->once())
            ->method('getCount')
            ->willReturn($count);
        $this->resourceMock->expects($this->once())
            ->method('getParentCount')
            ->willReturn($count);
        $this->renderMock->expects($this->once())
            ->method('renderRangeData')
            ->with($range, $count)
            ->willReturn($itemsData);

        $this->assertEquals($itemsData, $this->algorithm->getItemsData());
    }
}
