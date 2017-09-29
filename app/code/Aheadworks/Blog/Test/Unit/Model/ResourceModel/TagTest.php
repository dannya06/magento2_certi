<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\Tag
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    const TAG_NAME = 'tag';

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag
     */
    private $tagResourceModel;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

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

        $this->tagModel = $this->getMockBuilder('Aheadworks\Blog\Model\Tag')
            ->setMethods(['getName'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->tagResourceModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\ResourceModel\Tag',
            ['context' => $contextStub]
        );
    }

    /**
     * Testing that a row is fetched from the DB table
     */
    public function testIsNameUniqueFetch()
    {
        $this->tagModel->expects($this->any())
            ->method('getName')
            ->willReturn(self::TAG_NAME);
        $this->connection->expects($this->atLeastOnce())
            ->method('fetchRow')
            ->with($this->equalTo($this->select), $this->anything());
        $this->tagResourceModel->isNameUnique($this->tagModel);
    }

    /**
     * Testing of result of 'isNameUnique' method
     *
     * @dataProvider isNameUniqueResultDataProvider
     */
    public function testIsNameUniqueResult($fetchRowResult, $expectedResult)
    {
        $this->connection->expects($this->any())
            ->method('fetchRow')
            ->willReturn($fetchRowResult);
        $this->assertEquals($expectedResult, $this->tagResourceModel->isNameUnique($this->tagModel));
    }

    /**
     * @return array
     */
    public function isNameUniqueResultDataProvider()
    {
        return [
            'name is unique' => [false, true],
            'name is not unique' => [['fieldName' => 'fieldValue'], false]
        ];
    }

    /**
     * Testing return value of 'getValidationRulesBeforeSave' method
     */
    public function testGetValidationRulesBeforeSaveResult()
    {
        $this->assertInstanceOf(
            'Magento\Framework\Validator\DataObject',
            $this->tagResourceModel->getValidationRulesBeforeSave()
        );
    }
}
