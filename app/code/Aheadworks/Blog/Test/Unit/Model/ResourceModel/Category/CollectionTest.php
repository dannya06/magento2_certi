<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\Category\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    const MAIN_TABLE_NAME = 'aw_blog_cat';
    const CATEGORY_ID = 1;
    const CATEGORY_NAME = 'Category';
    const STORE_ID = 1;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection
     */
    private $categoryCollection;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;

    /**
     * @var \Aheadworks\Blog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryModel;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->setMethods(['from', 'joinLeft', 'where', 'group'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->select->expects($this->any())
            ->method('from')
            ->will($this->returnSelf());
        $this->select->expects($this->any())
            ->method('joinLeft')
            ->will($this->returnSelf());
        $this->select->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());
        $this->select->expects($this->any())
            ->method('group')
            ->will($this->returnSelf());

        $connectionStub = $this->getMockForAbstractClass('Magento\Framework\DB\Adapter\AdapterInterface');
        $connectionStub->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->select));
        $resourceStub = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Category')
            ->setMethods(['getConnection', 'getMainTable', 'getTable', 'getIdFieldName'])
            ->disableOriginalConstructor()
            ->getMock();
        $resourceStub->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connectionStub));
        $resourceStub->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue(self::MAIN_TABLE_NAME));
        $resourceStub->expects($this->any())
            ->method('getTable')
            ->will($this->returnArgument(0));
        $resourceStub->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('cat_id'));

        $this->categoryModel = $this->getMockBuilder('Aheadworks\Blog\Model\Category')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryModel->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap(
                [
                    ['cat_id', null, self::CATEGORY_ID],
                    ['name', null, self::CATEGORY_NAME]
                ]
            ));

        $this->categoryCollection = $objectManager->getObject(
            'Aheadworks\Blog\Model\ResourceModel\Category\Collection',
            ['resource' => $resourceStub]
        );
    }

    /**
     * Testing converting to option array
     */
    public function testToOptionArray()
    {
        /** @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection $categoryCollectionMock */
        $categoryCollectionMock = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Category\Collection')
            ->setMethods(['getIterator'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryCollectionMock->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$this->categoryModel]));
        $this->assertEquals(
            [['value' => self::CATEGORY_ID, 'label' => self::CATEGORY_NAME]],
            $categoryCollectionMock->toOptionArray()
        );
    }
}
