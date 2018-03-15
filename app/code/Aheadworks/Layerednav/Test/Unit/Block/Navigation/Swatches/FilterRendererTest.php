<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Block\Navigation\Swatches;

use Aheadworks\Layerednav\Block\Navigation\Swatches\FilterRenderer;
use Aheadworks\Layerednav\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Layer\State;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Block\Navigation\Swatches\FilterRenderer
 */
class FilterRendererTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterRenderer
     */
    private $renderer;

    /**
     * @var Layer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods(['getState'])
            ->disableOriginalConstructor()
            ->getMock();
        $layerResolverMock = $this->getMockBuilder(LayerResolver::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $layerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($this->layerMock);

        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['hideEmptyAttributeValues'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->renderer = $objectManager->getObject(
            FilterRenderer::class,
            [
                'layerResolver' => $layerResolverMock,
                'config' => $this->configMock
            ]
        );
    }

    /**
     * @param FilterInterface[]|\PHPUnit_Framework_MockObject_MockObject[] $currentFilterItemMocks
     * @param string $value
     * @param string $code
     * @param bool $isActive
     * @dataProvider isActiveItemDataProvider
     */
    public function testIsActiveItem($currentFilterItemMocks, $value, $code, $isActive)
    {
        $stateMock = $this->getMockBuilder(State::class)
            ->setMethods(['getFilters'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->layerMock->expects($this->once())
            ->method('getState')
            ->willReturn($stateMock);
        $stateMock->expects($this->once())
            ->method('getFilters')
            ->willReturn($currentFilterItemMocks);
        $this->assertSame($isActive, $this->renderer->isActiveItem($code, $value));
    }

    /**
     * Create filter item mock
     *
     * @param string $value
     * @param string $requestVar
     * @return FilterItem|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createFilterItemMock($value, $requestVar)
    {
        $filterItemMock = $this->getMockBuilder(FilterItem::class)
            ->setMethods(['getValue', 'getFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterItemMock->expects($this->once())
            ->method('getValue')
            ->willReturn($value);
        $filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getRequestVar')
            ->willReturn($requestVar);
        $filterItemMock->expects($this->once())
            ->method('getFilter')
            ->willReturn($filterMock);
        return $filterItemMock;
    }

    /**
     * @return array
     */
    public function isActiveItemDataProvider()
    {
        return [
            'active' => [
                [$this->createFilterItemMock('filter_value', 'request_var')],
                'filter_value',
                'request_var',
                true
            ],
            'inactive, different value' => [
                [$this->createFilterItemMock('filter_value1', 'request_var')],
                'filter_value2',
                'request_var',
                false
            ],
            'inactive, request var doesn\'t match code' => [
                [$this->createFilterItemMock('filter_value', 'request_var1')],
                'filter_value',
                'request_var2',
                false
            ],
            'inactive, no active filter items' => [
                [],
                'filter_value',
                'request_var',
                false
            ]
        ];
    }

    /**
     * Test isNeedToShowOption method
     *
     * @param bool $hideEmptyAttributeValues
     * @param FilterInterface[]|\PHPUnit_Framework_MockObject_MockObject[] $currentFilterItemMocks
     * @param string $value
     * @param string $code
     * @param array $label
     * @param bool $result
     * @throws \Magento\Framework\Exception\LocalizedException
     * @dataProvider isNeedToShowOptionDataProvider
     */
    public function testIsNeedToShowOption(
        $hideEmptyAttributeValues,
        $currentFilterItemMocks,
        $value,
        $code,
        $label,
        $result
    ) {
        $this->configMock->expects($this->once())
            ->method('hideEmptyAttributeValues')
            ->willReturn($hideEmptyAttributeValues);

        $stateMock = $this->getMockBuilder(State::class)
            ->setMethods(['getFilters'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->layerMock->expects($this->any())
            ->method('getState')
            ->willReturn($stateMock);
        $stateMock->expects($this->any())
            ->method('getFilters')
            ->willReturn($currentFilterItemMocks);

        $this->assertEquals($result, $this->renderer->isNeedToShowOption($code, $value, $label));
    }

    /**
     * @return array
     */
    public function isNeedToShowOptionDataProvider()
    {
        return [
            'hide empty attributes disabled' => [
                false,
                [],
                'filter_value',
                'request_var',
                ['count' => 1],
                true
            ],
            'zero count' => [
                true,
                [],
                'filter_value',
                'request_var',
                ['count' => 0],
                false
            ],
            'no count' => [
                true,
                [],
                'filter_value',
                'request_var',
                [],
                false
            ],
            'active option' => [
                true,
                [$this->createFilterItemMock('filter_value', 'request_var')],
                'filter_value',
                'request_var',
                [],
                true
            ],
        ];
    }
}
