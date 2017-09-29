<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel\Post;

use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\Post\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    const MAIN_TABLE_NAME = 'aw_blog_post';
    const NOW = '2016-03-23 08:11:33';
    const STORE_ID = 1;
    const CATEGORY_ID = 1;
    const TAG_ID = 1;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Post\Collection
     */
    private $postCollection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;

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

        $this->connection = $this->getMockForAbstractClass('Magento\Framework\DB\Adapter\AdapterInterface');
        $this->connection->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->select));
        $resourceStub = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Post')
            ->setMethods(['getConnection', 'getMainTable', 'getTable', 'getIdFieldName'])
            ->disableOriginalConstructor()
            ->getMock();
        $resourceStub->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->connection));
        $resourceStub->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue(self::MAIN_TABLE_NAME));
        $resourceStub->expects($this->any())
            ->method('getTable')
            ->will($this->returnArgument(0));
        $resourceStub->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('post_id'));

        $this->postCollection = $objectManager->getObject(
            'Aheadworks\Blog\Model\ResourceModel\Post\Collection',
            ['resource' => $resourceStub]
        );
    }

    /**
     * Testing that a WHERE clause added to the query correctly
     *
     * @dataProvider addVirtualStatusFilterDataProvider
     */
    public function testAddVirtualStatusFilterWhere($statuses, $whereClause)
    {
        $this->connection->expects($this->any())
            ->method('quoteInto')
            ->will($this->returnCallback(
                function ($text, $value) {
                    $result = "";
                    if ($text == 'status = ?') {
                        if ($value == Status::DRAFT) {
                            $result = "status = 'draft'";
                        }
                        if ($value == Status::PUBLICATION) {
                            $result = "status = 'publication'";
                        }
                    }
                    if ($text == "publish_date <= ?") {
                        $result = "publish_date <= '" . self::NOW . "'";
                    }
                    if ($text == "publish_date > ?") {
                        $result = "publish_date > '" . self::NOW . "'";
                    }
                    return $result;
                }
            ));
        $this->select->expects($this->once())
            ->method('where')
            ->with($this->equalTo($whereClause));
        $this->postCollection->addVirtualStatusFilter($statuses);
    }

    /**
     * Testing return value of 'addVirtualStatusFilter' method
     */
    public function testAddVirtualStatusFilterResult()
    {
        $this->assertSame(
            $this->postCollection,
            $this->postCollection->addVirtualStatusFilter(Status::DRAFT)
        );
    }

    /**
     * Testing of adding category filter to collection
     */
    public function testAddCategoryFilter()
    {
        $this->postCollection->addCategoryFilter(self::CATEGORY_ID);
        $filter = $this->postCollection->getFilter('cat_id');
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
            $this->postCollection,
            $this->postCollection->addCategoryFilter(self::CATEGORY_ID)
        );
    }

    /**
     * Testing of adding tag filter to collection
     */
    public function testAddTagFilter()
    {
        $this->postCollection->addTagFilter(self::TAG_ID);
        $filter = $this->postCollection->getFilter('tag_id');
        $this->assertNotNull($filter);
        $this->assertEquals('tag_id', $filter['field']);
        $this->assertEquals(['in' => [self::TAG_ID]], $filter['value']);
        $this->assertEquals('public', $filter['type']);
    }

    /**
     * Testing return value of 'addTagFilter' method
     */
    public function testAddTagFilterResult()
    {
        $this->assertSame(
            $this->postCollection,
            $this->postCollection->addTagFilter(self::TAG_ID)
        );
    }

    /**
     * @return array
     */
    public function addVirtualStatusFilterDataProvider()
    {
        return [
            'draft' => [
                [Status::DRAFT],
                'status = \'draft\''
            ],
            'published' => [
                [Status::PUBLICATION_PUBLISHED],
                'status = \'publication\' AND publish_date <= \'' . self::NOW . '\''
            ],
            'scheduled' => [
                [Status::PUBLICATION_SCHEDULED],
                'status = \'publication\' AND publish_date > \'' . self::NOW . '\''
            ]
        ];
    }
}
