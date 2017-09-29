<?php
namespace Aheadworks\Blog\Test\Unit\Model\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Source\Tags
 */
class TagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\Source\Tags
     */
    private $tagsSourceModel;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagsCollection;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->tagsCollection = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Tag\Collection')
            ->setMethods(['toOptionArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagsCollectionFactoryStub = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Tag\CollectionFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagsCollectionFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->tagsCollection));
        $this->tagsSourceModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Source\Tags',
            ['tagCollectionFactory' => $tagsCollectionFactoryStub]
        );
    }

    /**
     * Testing 'toOptionArray' method call
     */
    public function testToOptionArray()
    {
        $optionArray = [['option value' => 'option label']];
        $this->tagsCollection->expects($this->atLeastOnce())
            ->method('toOptionArray')
            ->willReturn($optionArray);
        $this->assertEquals($optionArray, $this->tagsSourceModel->toOptionArray());
    }
}
