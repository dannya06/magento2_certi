<?php
namespace Aheadworks\Blog\Test\Unit\Model\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Source\Categories
 */
class CategoriesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\Source\Categories
     */
    private $categoriesSourceModel;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryCollection;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->categoryCollection = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Category\Collection')
            ->setMethods(['toOptionArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryCollectionFactoryStub = $this->getMockBuilder(
            'Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryCollectionFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->categoryCollection));
        $this->categoriesSourceModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Source\Categories',
            ['categoryCollectionFactory' => $categoryCollectionFactoryStub]
        );
    }

    /**
     * Testing 'toOptionArray' method call
     */
    public function testToOptionArray()
    {
        $optionArray = [['option value' => 'option label']];
        $this->categoryCollection->expects($this->atLeastOnce())
            ->method('toOptionArray')
            ->willReturn($optionArray);
        $this->assertEquals($optionArray, $this->categoriesSourceModel->toOptionArray());
    }
}
