<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Layer\Filter\DataProvider\Decimal as DataProvider;
use Aheadworks\Layerednav\Model\Layer\Filter\DataProvider\DecimalFactory as DataProviderFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\Decimal as DecimalFilter;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\ConditionRegistry;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter\Decimal as ResourceDecimal;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\State;
use Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder as ItemDataBuilder;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Decimal
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DecimalTest extends \PHPUnit\Framework\TestCase
{
    const ATTRIBUTE_CODE = 'decimal_attribute_code';

    /**
     * @var DecimalFilter
     */
    private $filter;

    /**
     * @var Layer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerMock;

    /**
     * @var ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterItemFactoryMock;

    /**
     * @var ItemDataBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemDataBuilderMock;

    /**
     * @var AlgorithmFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $algorithmFactoryMock;

    /**
     * @var DataProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProviderMock;

    /**
     * @var ConditionRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionRegistryMock;

    /**
     * @var Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeMock;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods(['getState'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterItemFactoryMock = $this->getMockBuilder(ItemFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemDataBuilderMock = $this->getMockBuilder(ItemDataBuilder::class)
            ->setMethods(['addItemData', 'build'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->algorithmFactoryMock = $this->getMockBuilder(AlgorithmFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataProviderMock = $this->getMockBuilder(DataProvider::class)
            ->setMethods(['getIntervals', 'getResource', 'getRange', 'getRangeItemCounts'])
            ->disableOriginalConstructor()
            ->getMock();
        $dataProviderFactoryMock = $this->getMockBuilder(DataProviderFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $dataProviderFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->dataProviderMock);
        $this->conditionRegistryMock = $this->getMockBuilder(ConditionRegistry::class)
            ->setMethods(['addConditions'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeMock = $this->getMockBuilder(Attribute::class)
            ->setMethods(['getFrontend', 'getIsFilterable', 'getAttributeCode'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeMock->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn(self::ATTRIBUTE_CODE);

        $this->filter = $objectManager->getObject(
            DecimalFilter::class,
            [
                'filterItemFactory' => $this->filterItemFactoryMock,
                'layer' => $this->layerMock,
                'itemDataBuilder' => $this->itemDataBuilderMock,
                'algorithmFactory' => $this->algorithmFactoryMock,
                'dataProviderFactory' => $dataProviderFactoryMock,
                'conditionsRegistry' => $this->conditionRegistryMock,
                'data' => ['attribute_model' => $this->attributeMock]
            ]
        );
    }

    public function testApply()
    {
        $from = '1.00';
        $to = '2.00';
        $conditions = ['filter_value > 1.00 AND filter_value < 2.00'];

        /** @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $resourceMock = $this->getMockBuilder(ResourceDecimal::class)
            ->setMethods(['joinFilterToCollection', 'getWhereConditions'])
            ->disableOriginalConstructor()
            ->getMock();
        $stateMock = $this->getMockBuilder(State::class)
            ->setMethods(['addFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterItemMock = $this->getMockBuilder(FilterItem::class)
            ->setMethods(['setFilter', 'setLabel', 'setValue', 'setCount'])
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo(self::ATTRIBUTE_CODE))
            ->willReturn($from . '-' . $to);
        $this->dataProviderMock->expects($this->once())
            ->method('getIntervals')
            ->with($this->equalTo([$from . '-' . $to]))
            ->willReturn([[$from, $to]]);
        $this->dataProviderMock->expects($this->any())
            ->method('getResource')
            ->willReturn($resourceMock);
        $resourceMock->expects($this->once())
            ->method('joinFilterToCollection')
            ->with($this->filter);
        $resourceMock->expects($this->once())
            ->method('getWhereConditions')
            ->with(
                $this->equalTo($this->filter),
                $this->equalTo([[$from, $to]])
            )
            ->willReturn($conditions);
        $this->conditionRegistryMock->expects($this->once())
            ->method('addConditions')
            ->with($this->equalTo(self::ATTRIBUTE_CODE), $this->equalTo($conditions));
        $this->layerMock->expects($this->once())
            ->method('getState')
            ->willReturn($stateMock);
        $this->filterItemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterItemMock);
        $filterItemMock->expects($this->once())
            ->method('setFilter')
            ->with($this->equalTo($this->filter))
            ->willReturnSelf();
        $filterItemMock->expects($this->once())
            ->method('setLabel')
            ->with($this->equalTo(self::ATTRIBUTE_CODE))
            ->willReturnSelf();
        $filterItemMock->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo($from . '-' . $to))
            ->willReturnSelf();
        $filterItemMock->expects($this->once())
            ->method('setCount')
            ->with($this->equalTo(0))
            ->willReturnSelf();
        $stateMock->expects($this->once())
            ->method('addFilter')
            ->with($this->equalTo($filterItemMock));

        $this->assertSame($this->filter, $this->filter->apply($requestMock));
    }
}
