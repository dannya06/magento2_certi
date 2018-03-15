<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Block;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Applier;
use Aheadworks\Layerednav\Block\Navigation;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\DataSource\CompositeConfigProvider;
use Aheadworks\Layerednav\Model\Layer\FilterListAbstract;
use Aheadworks\Layerednav\Model\Layer\FilterListResolver;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\AvailabilityFlagInterface;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Layer\State;
use Magento\Catalog\Model\Layer\Filter\FilterInterface as LayerFilterInterface;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Model\Layout\Merge as LayoutMerge;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Test for \Aheadworks\Layerednav\Block\Navigation
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NavigationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Navigation
     */
    private $block;

    /**
     * @var Layer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerMock;

    /**
     * @var FilterListAbstract|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterListMock;

    /**
     * @var AvailabilityFlagInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $visibilityFlagMock;

    /**
     * @var Applier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $applierMock;

    /**
     * @var PageTypeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pageTypeResolverMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var CompositeConfigProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataSourceConfigProviderMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods(['getState', 'getCurrentCategory'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterListMock = $this->getMockBuilder(FilterListAbstract::class)
            ->setMethods(['getFilters'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->visibilityFlagMock = $this->getMockForAbstractClass(AvailabilityFlagInterface::class);
        $this->applierMock = $this->getMockBuilder(Applier::class)
            ->setMethods(['applyFilters'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTypeResolverMock = $this->getMockBuilder(PageTypeResolver::class)
            ->setMethods(['getType'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['isAjaxEnabled', 'isPopoverDisabled'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataSourceConfigProviderMock = $this->getMockBuilder(CompositeConfigProvider::class)
            ->setMethods(['getConfig'])
            ->disableOriginalConstructor()
            ->getMock();

        $layerResolverMock = $this->getMockBuilder(LayerResolver::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $layerResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($this->layerMock);
        $filterListResolverMock = $this->getMockBuilder(FilterListResolver::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterListResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($this->filterListMock);

        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);
        $context = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
                'storeManager' => $this->storeManagerMock,
                'layout' => $this->layoutMock
            ]
        );

        $this->block = $objectManager->getObject(
            Navigation::class,
            [
                'context' => $context,
                'layerResolver' => $layerResolverMock,
                'filterListResolver' => $filterListResolverMock,
                'visibilityFlag' => $this->visibilityFlagMock,
                'applier' => $this->applierMock,
                'pageTypeResolver' => $this->pageTypeResolverMock,
                'config' => $this->configMock,
                'dataSourceConfigProvider' => $this->dataSourceConfigProviderMock
            ]
        );
    }

    public function testPrepareLayout()
    {
        $this->applierMock->expects($this->once())
            ->method('applyFilters')
            ->with($this->layerMock);

        $class = new \ReflectionClass($this->block);
        $method = $class->getMethod('_prepareLayout');
        $method->setAccessible(true);

        $this->assertEquals($this->block, $method->invoke($this->block));
    }

    public function testToHtmlWhenFilterDisable()
    {
        $filterMock = $this->getMockForAbstractClass(AbstractFilter::class, [], '', false, false, true);
        $this->filterListMock->expects($this->once())
            ->method('getFilters')
            ->with($this->equalTo($this->layerMock))
            ->willReturn([$filterMock]);
        $this->visibilityFlagMock->expects($this->once())
            ->method('isEnabled')
            ->with(
                $this->equalTo($this->layerMock),
                $this->equalTo([$filterMock])
            )
            ->willReturn(false);
        $this->assertEquals('', $this->block->toHtml());
    }

    public function testGetFilters()
    {
        $filterMock = $this->getMockForAbstractClass(AbstractFilter::class, [], '', false, false, true);
        $this->filterListMock->expects($this->once())
            ->method('getFilters')
            ->with($this->equalTo($this->layerMock))
            ->willReturn([$filterMock]);
        $this->assertEquals([$filterMock], $this->block->getFilters());
    }

    /**
     * @param AbstractFilter[]|\PHPUnit_Framework_MockObject_MockObject[] $filters
     * @param bool $hasActiveFilters
     * @dataProvider hasActiveFiltersDataProvider
     */
    public function testHasActiveFilters($filters, $hasActiveFilters)
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
            ->willReturn($filters);
        $this->assertSame($hasActiveFilters, $this->block->hasActiveFilters());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsAjaxEnabled($value)
    {
        $this->configMock->expects($this->once())
            ->method('isAjaxEnabled')
            ->willReturn($value);
        $this->assertSame($value, $this->block->isAjaxEnabled());
    }

    public function testGetDataSourceConfig()
    {
        $configData = ['dataField' => 'dataValue'];

        $this->dataSourceConfigProviderMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($configData);

        $this->assertEquals($configData, $this->block->getDataSourceConfig());
    }

    /**
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsPopoverDisabled($value)
    {
        $this->configMock->expects($this->once())
            ->method('isPopoverDisabled')
            ->willReturn($value);
        $this->assertSame($value, $this->block->isPopoverDisabled());
    }

    /**
     * @return array
     */
    public function hasActiveFiltersDataProvider()
    {
        return [
            'has active filters' => [
                [
                    $this->getMockForAbstractClass(AbstractFilter::class, [], '', false, false, true)
                ],
                true
            ],
            'has no active filters' => [[], false]
        ];
    }

    /**
     * @return array
     */
    public function getSearchQueryTextDataProvider()
    {
        return [
            [PageTypeResolver::PAGE_TYPE_CATEGORY, null, ''],
            [PageTypeResolver::PAGE_TYPE_CATALOG_SEARCH, 'query text', 'query text']
        ];
    }

    /**
     * @return array
     */
    public function isAjaxEnabledDataProvider()
    {
        return [[true], [false]];
    }

    /**
     * @return array
     */
    public function boolDataProvider()
    {
        return [[true], [false]];
    }

    /**
     * Test isFilterExpanded method
     *
     * @param int $displayState
     * @dataProvider isFilterExpandedDataProvider
     */
    public function testIsFilterExpanded($layout, $displayState, $result)
    {
        $layoutUpdateMock = $this->getMockBuilder(LayoutMerge::class)
            ->setMethods(['getPageLayout'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutUpdateMock->expects($this->once())
            ->method('getPageLayout')
            ->willReturn($layout);
        $this->layoutMock->expects($this->once())
            ->method('getUpdate')
            ->willReturn($layoutUpdateMock);

        $filterMock = $this->getMockBuilder(AbstractFilter::class)
            ->setMethods(['getStorefrontDisplayState'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterMock->expects($this->any())
            ->method('getStorefrontDisplayState')
            ->willReturn($displayState);

        $this->assertEquals($result, $this->block->isFilterExpanded($filterMock));
    }

    /**
     * @return array
     */
    public function isFilterExpandedDataProvider()
    {
        return [
            [
                'layout'        => '2columns-left',
                'displayState'  => FilterInterface::DISPLAY_STATE_EXPANDED,
                'result'        => true
            ],
            [
                'layout'        => '2columns-left',
                'displayState'  => FilterInterface::DISPLAY_STATE_COLLAPSED,
                'result'        => false
            ],
            [
                'layout'        => '1column',
                'displayState'  => FilterInterface::DISPLAY_STATE_EXPANDED,
                'result'        => false
            ],
            [
                'layout'        => '1column',
                'displayState'  => FilterInterface::DISPLAY_STATE_COLLAPSED,
                'result'        => false
            ],
        ];
    }

    /**
     * Test isFilterActive method
     *
     * @param FilterItem[] $filterItems
     * @param string $requestVar
     * @param bool $result
     * @dataProvider getIsActiveFilterDataProvider
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testIsFilterActive($filterItems, $requestVar, $result)
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
            ->willReturn($filterItems);

        $layerFilterMock = $this->getMockBuilder(LayerFilterInterface::class)
            ->getMockForAbstractClass();
        $layerFilterMock->expects($this->any())
            ->method('getRequestVar')
            ->willReturn($requestVar);

        $this->assertEquals($result, $this->block->isFilterActive($layerFilterMock));
    }

    /**
     * @return array
     */
    public function getIsActiveFilterDataProvider()
    {
        return [
            [
                'filterItems' => [],
                'requestVar' => 'var1',
                false
            ],
            [
                'filterItems' => [$this->getFilterItemMock('var1')],
                'requestVar' => 'var2',
                false
            ],
            [
                'filterItems' => [$this->getFilterItemMock('var1')],
                'requestVar' => 'var1',
                true
            ],
        ];
    }

    /**
     * @param string $requestVar
     * @return FilterItem|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getFilterItemMock($requestVar)
    {
        $filterMock = $this->getMockBuilder(LayerFilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getRequestVar')
            ->willReturn($requestVar);
        $filterItemMock = $this->getMockBuilder(FilterItem::class)
            ->setMethods(['getFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterItemMock->expects($this->once())
            ->method('getFilter')
            ->willReturn($filterMock);

        return $filterItemMock;
    }
}
