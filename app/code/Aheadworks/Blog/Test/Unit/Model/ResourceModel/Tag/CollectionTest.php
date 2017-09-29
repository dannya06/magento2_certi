<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel\Tag;

use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\Tag\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    const MAIN_TABLE_NAME = 'aw_blog_tag';
    const TAG_ID = 1;
    const TAG_NAME = 'tag';
    const CATEGORY_ID = 1;
    const STORE_ID = 1;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection
     */
    private $tagCollection;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;

    /**
     * @var \Aheadworks\Blog\Model\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagModel;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->setMethods(['from', 'joinLeft', 'where', 'group', 'columns'])
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
        $this->select->expects($this->any())
            ->method('columns')
            ->will($this->returnSelf());

        $connectionStub = $this->getMockForAbstractClass('Magento\Framework\DB\Adapter\AdapterInterface');
        $connectionStub->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->select));
        $resourceStub = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Tag')
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
            ->will($this->returnValue('id'));

        $this->tagModel = $this->getMockBuilder('Aheadworks\Blog\Model\Tag')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagModel->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap(
                [
                    ['id', null, self::TAG_ID],
                    ['name', null, self::TAG_NAME]
                ]
            ));

        $this->tagCollection = $objectManager->getObject(
            'Aheadworks\Blog\Model\ResourceModel\Tag\Collection',
            ['resource' => $resourceStub]
        );
    }

    /**
     * Testing converting to option array
     */
    public function testToOptionArray()
    {
        /** @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection $tagCollectionMock */
        $tagCollectionMock = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Tag\Collection')
            ->setMethods(['getIterator'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagCollectionMock->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$this->tagModel]));
        $this->assertEquals(
            [['value' => self::TAG_ID, 'label' => self::TAG_NAME]],
            $tagCollectionMock->toOptionArray()
        );
    }

    /**
     * Testing of adding category filter to collection
     */
    public function testAddCategoryFilter()
    {
        $this->tagCollection->addCategoryFilter(self::CATEGORY_ID);
        $filter = $this->tagCollection->getFilter('cat_id');
        $this->assertNotNull($filter);
        $this->assertEquals('cat_id', $filter['field']);
        $this->assertEquals(['in' => [self::CATEGORY_ID]], $filter['value']);
        $this->assertEquals('public', $filter['type']);
    }

    /**
     * Testing return value of 'addCategoryFilter' method
     */
    public function testAddCategoryFilterResult()
    {
        $this->assertSame(
            $this->tagCollection,
            $this->tagCollection->addCategoryFilter(self::CATEGORY_ID)
        );
    }

    /**
     * Testing that a JOIN added to the query correctly
     * during execution of 'joinCount' method
     */
    public function testJoinCountJoins()
    {
        $this->select->expects($this->exactly(3))
            ->method('joinLeft')
            ->withConsecutive(
                [
                    $this->equalTo(['post_linkage_table_count' => 'aw_blog_post_tag']),
                    $this->equalTo('main_table.id = post_linkage_table_count.tag_id'),
                    $this->equalTo(['post_id'])
                ],
                [
                    $this->equalTo(['post_table' => 'aw_blog_post']),
                    $this->equalTo('post_linkage_table_count.post_id = post_table.post_id'),
                    $this->anything()
                ],
                [
                    $this->equalTo(['store_linkage_table' => 'aw_blog_post_store']),
                    $this->equalTo('post_table.post_id = store_linkage_table.post_id'),
                    $this->anything()
                ]
            );
        $this->tagCollection->joinCount(self::STORE_ID);
    }

    /**
     * Testing that 'count' column is added to result
     * during execution of 'joinCount' method
     */
    public function testJoinCountAddCountColumn()
    {
        $this->select->expects($this->once())
            ->method('columns')
            ->with(
                $this->callback(
                    function ($cols) {
                        return array_key_exists('count', $cols);
                    }
                )
            );
        $this->tagCollection->joinCount(self::STORE_ID);
    }

    /**
     * Testing that a WHERE clause added to the query correctly
     * during execution of 'joinCount' method
     */
    public function testJoinCountWhere()
    {
        $this->select->expects($this->exactly(3))
            ->method('where')
            ->withConsecutive(
                [
                    $this->equalTo('store_linkage_table.store_id IN(?)'),
                    $this->contains(self::STORE_ID)
                ],
                [
                    $this->equalTo('post_table.status = ?'),
                    $this->equalTo(PostStatus::PUBLICATION)
                ],
                [
                    $this->equalTo('post_table.publish_date <= ?'),
                    $this->anything()
                ]
            );
        $this->tagCollection->joinCount(self::STORE_ID);
    }

    /**
     * Testing that a grouping added to the query correctly
     * during execution of 'joinCount' method
     */
    public function testJoinCountGroup()
    {
        $this->select->expects($this->atLeastOnce())
            ->method('group')
            ->with($this->equalTo('main_table.id'));
        $this->tagCollection->joinCount(self::STORE_ID);
    }
    /**
     * Testing return value of 'joinCount' method
     */
    public function testJoinCountResult()
    {
        $this->assertSame(
            $this->tagCollection,
            $this->tagCollection->joinCount(self::STORE_ID)
        );
    }
}
