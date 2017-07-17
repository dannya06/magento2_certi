<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Model\Source\ProductAttributes;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Model\Source\ProductAttributes\Attributes;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Api\SearchCriteria;
use Magento\Catalog\Api\Data\ProductAttributeSearchResultsInterface;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Source\ProductAttributes\Attributes
 */
class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Attributes
     */
    private $model;

    /**
     * @var ProductAttributeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productAttributeRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->productAttributeRepositoryMock = $this->getMockForAbstractClass(
            ProductAttributeRepositoryInterface::class
        );
        $this->searchCriteriaBuilderMock = $this->getMock(SearchCriteriaBuilder::class, ['create'], [], '', false);

        $this->model = $objectManager->getObject(
            Attributes::class,
            [
                'productAttributeRepository' => $this->productAttributeRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock
            ]
        );
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $searchCriteriaMock = $this->getMock(SearchCriteria::class, [], [], '', false);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($searchCriteriaMock));
        $attributeMock = $this->getMock(
            Attribute::class,
            [
                'isAllowedForRuleCondition',
                'getIsUsedForPromoRules',
                'getAttributeCode',
                'getFrontendLabel',
                'getFrontendInput',
                'getSource'
            ],
            [],
            '',
            false
        );
        $attributeMock->expects($this->once())
            ->method('isAllowedForRuleCondition')
            ->willReturn(true);
        $attributeMock->expects($this->once())
            ->method('getIsUsedForPromoRules')
            ->willReturn(true);
        $attributeMock->expects($this->exactly(2))
            ->method('getAttributeCode')
            ->willReturn('code');
        $attributeMock->expects($this->once())
            ->method('getFrontendLabel')
            ->willReturn('label');
        $attributeMock->expects($this->exactly(2))
            ->method('getFrontendInput')
            ->willReturn('text');
        $abstractSourceMock = $this->getMock(AbstractSource::class, ['getAllOptions'], [], '', false);
        $abstractSourceMock->expects($this->once())
            ->method('getAllOptions')
            ->willReturn([['label' => 'label', 'value' => 'value']]);
        $attributeMock->expects($this->once())
            ->method('getSource')
            ->willReturn($abstractSourceMock);

        $productAttributeSearchResultsMock = $this->getMockForAbstractClass(
            ProductAttributeSearchResultsInterface::class
        );
        $productAttributeSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$attributeMock]);

        $this->productAttributeRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn($productAttributeSearchResultsMock);
        $this->assertTrue(is_array($this->model->toOptionArray()));
    }
}
