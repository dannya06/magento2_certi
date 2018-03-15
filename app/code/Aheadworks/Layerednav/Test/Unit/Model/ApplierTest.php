<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model;

use Aheadworks\Layerednav\Model\Applier;
use Aheadworks\Layerednav\Model\Layer\FilterListAbstract;
use Aheadworks\Layerednav\Model\Layer\FilterListResolver;
use Aheadworks\Layerednav\Model\ResourceModel\Layer\ConditionRegistry;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Applier
 */
class ApplierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Applier
     */
    private $applier;

    /**
     * @var FilterListResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterListResolverMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var ConditionRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionRegistryMock;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterListResolverMock = $this->getMockBuilder(FilterListResolver::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->conditionRegistryMock = $this->getMockBuilder(ConditionRegistry::class)
            ->setMethods(['getConditions'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->applier = $objectManager->getObject(
            Applier::class,
            [
                'filterListResolver' => $this->filterListResolverMock,
                'request' => $this->requestMock,
                'conditionRegistry' => $this->conditionRegistryMock
            ]
        );
    }

    /**
     * @param array $conditions
     * @param string|null $whereCondition
     * @dataProvider applyFiltersDataProvider
     */
    public function testApplyFilters($conditions = [], $whereCondition = null)
    {
        /** @var Layer|\PHPUnit_Framework_MockObject_MockObject $layerMock */
        $layerMock = $this->getMockBuilder(Layer::class)
            ->setMethods(['getProductCollection', 'apply'])
            ->disableOriginalConstructor()
            ->getMock();
        $selectMock = $this->getMockBuilder(Select::class)
            ->setMethods(['where', 'group'])
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock = $this->getMockBuilder(ProductCollection::class)
            ->setMethods(['getSelect', 'getSize'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterMock = $this->getMockForAbstractClass(
            AbstractFilter::class,
            [],
            '',
            false,
            false,
            true,
            ['apply']
        );
        $filterListMock = $this->getMockBuilder(FilterListAbstract::class)
            ->setMethods(['getFilters'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->filterListResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($filterListMock);
        $filterListMock->expects($this->once())
            ->method('getFilters')
            ->with($this->equalTo($layerMock))
            ->willReturn([$filterMock]);
        $filterMock->expects($this->once())
            ->method('apply')
            ->with($this->equalTo($this->requestMock));
        $layerMock->expects($this->once())
            ->method('getProductCollection')
            ->willReturn($collectionMock);
        $collectionMock->expects($this->any())
            ->method('getSelect')
            ->willReturn($selectMock);

        $this->conditionRegistryMock->expects($this->once())
            ->method('getConditions')
            ->willReturn($conditions);
        if ($whereCondition) {
            $selectMock->expects($this->exactly(count($conditions)))
                ->method('where')
                ->with($this->equalTo($whereCondition));
            $selectMock->expects($this->once())
                ->method('group')
                ->with($this->equalTo('e.entity_id'));
        }
        $layerMock->expects($this->once())->method('apply');

        $this->applier->applyFilters($layerMock);
    }

    /**
     * @return array
     */
    public function applyFiltersDataProvider()
    {
        return [
            'filters applied' => [['attribute' => ['condition1', 'condition2']], '(condition1 OR condition2)'],
            'filters not applied' => [[], null]
        ];
    }
}
