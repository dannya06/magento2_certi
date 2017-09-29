<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\AbstractResource
 */
class AbstractResourceTest extends \PHPUnit_Framework_TestCase
{
    const URL_KEY = 'url-key';
    const POST_TABLE_NAME = 'aw_blog_post';
    const CATEGORY_TABLE_NAME = 'aw_blog_cat';

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\AbstractResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractResourceModel;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;

    /**
     * @var \Magento\Framework\Model\AbstractModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $model;

    public function setUp()
    {
        $this->select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->setMethods(['from', 'where'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->select->expects($this->any())
            ->method('from')
            ->will($this->returnSelf());
        $this->select->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());

        $this->connection = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->getMockForAbstractClass();
        $this->connection->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->select));

        $resourcesStub = $this->getMockBuilder('Magento\Framework\App\ResourceConnection')
            ->setMethods(['getConnection', 'getTableName'])
            ->disableOriginalConstructor()
            ->getMock();
        $resourcesStub->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->connection));
        $resourcesStub->expects($this->any())
            ->method('getTableName')
            ->will($this->returnArgument(0));

        $transactionManagerStub = $this->getMockBuilder(
            'Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface'
        )
            ->getMockForAbstractClass();
        $transactionManagerStub->expects($this->any())
            ->method('start')
            ->will($this->returnValue($this->connection));

        $objectRelationProcessorStub = $this->getMockBuilder(
            'Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $contextStub = $this->getMockBuilder('Magento\Framework\Model\ResourceModel\Db\Context')
            ->setMethods(['getResources', 'getTransactionManager', 'getObjectRelationProcessor'])
            ->disableOriginalConstructor()
            ->getMock();
        $contextStub->expects($this->any())
            ->method('getResources')
            ->will($this->returnValue($resourcesStub));
        $contextStub->expects($this->any())
            ->method('getTransactionManager')
            ->will($this->returnValue($transactionManagerStub));
        $contextStub->expects($this->any())
            ->method('getObjectRelationProcessor')
            ->will($this->returnValue($objectRelationProcessorStub));

        $this->model = $this->getMockBuilder('Aheadworks\Blog\Model\Tag')
            ->setMethods(['getUrlKey'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->abstractResourceModel = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\AbstractResource')
            ->setConstructorArgs(['context' => $contextStub])
            ->getMockForAbstractClass();
    }

    /**
     * Testing that a row is fetched from the DB table
     */
    public function testIsUrlKeyUniqueFetch()
    {
        $this->model->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::URL_KEY);
        $this->connection->expects($this->atLeastOnce())
            ->method('fetchRow')
            ->with($this->equalTo($this->select), $this->anything());
        $this->abstractResourceModel->isUrlKeyUnique($this->model);
    }

    /**
     * Testing that rows with 'url_key' are searched in the post and category tables
     */
    public function testIsUrlKeyUniqueWalkThroughTables()
    {
        $this->select->expects($this->exactly(2))
            ->method('from')
            ->with(
                $this->logicalXor(
                    $this->equalTo(self::POST_TABLE_NAME),
                    $this->equalTo(self::CATEGORY_TABLE_NAME)
                )
            );
        $this->abstractResourceModel->isUrlKeyUnique($this->model);
    }

    /**
     * Testing of result of 'isUrlKeyUnique' method
     *
     * @dataProvider isUrlKeyUniqueResultDataProvider
     */
    public function testIsUrlKeyUniqueResult($fetchRowResult, $expectedResult)
    {
        $this->connection->expects($this->any())
            ->method('fetchRow')
            ->willReturn($fetchRowResult);
        $this->assertEquals($expectedResult, $this->abstractResourceModel->isUrlKeyUnique($this->model));
    }

    /**
     * @return array
     */
    public function isUrlKeyUniqueResultDataProvider()
    {
        return [
            'url key is unique' => [false, true],
            'url key is not unique' => [['fieldName' => 'fieldValue'], false]
        ];
    }
}
