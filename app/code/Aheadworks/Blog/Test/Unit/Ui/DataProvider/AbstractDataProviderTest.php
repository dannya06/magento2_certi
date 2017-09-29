<?php
namespace Aheadworks\Blog\Test\Unit\Ui\DataProvider;

/**
 * Test for \Aheadworks\Blog\Ui\DataProvider\AbstractDataProvider
 */
class AbstractDataProviderTest extends \PHPUnit_Framework_TestCase
{
    const DATA_PROVIDER_NAME = 'abstract_data_source';
    const PRIMARY_FIELD_NAME = 'primary_id';
    const REQUEST_FIELD_NAME = 'request_id';
    const FIELD_SET_NAME = 'fieldSet';
    const FIELD_NAME = 'fieldName';
    const FIELD_META = 'fieldMeta';

    /**
     * @var array
     */
    private $providerConfig = ['configField' => 'configValue'];

    /**
     * @var array
     */
    private $providerData = ['dataField' => 'dataValue'];

    /**
     * @var array
     */
    private $providerMeta = ['metaField' => 'metaValue'];

    /**
     * @var array
     */
    private $providerFields = [self::FIELD_NAME => self::FIELD_META];

    /**
     * @var \Aheadworks\Blog\Ui\DataProvider\AbstractDataProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProvider;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteria;

    public function setUp()
    {
        $this->searchCriteria = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->setMethods(['setRequestName'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchCriteriaBuilder = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaBuilder')
            ->setMethods(
                [
                    'addFilters',
                    'setSortOrders',
                    'setPageSize',
                    'setCurrentPage',
                    'create'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('addFilters')
            ->will($this->returnSelf());
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('setSortOrders')
            ->will($this->returnSelf());
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('setPageSize')
            ->will($this->returnSelf());
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('setCurrentPage')
            ->will($this->returnSelf());
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->searchCriteria));

        $this->providerMeta[self::FIELD_SET_NAME] = ['fields' => $this->providerFields];
        $this->providerData['config'] = $this->providerConfig;
        $this->dataProvider = $this->getMockBuilder('Aheadworks\Blog\Ui\DataProvider\AbstractDataProvider')
            ->setConstructorArgs(
                [
                    self::DATA_PROVIDER_NAME,
                    self::PRIMARY_FIELD_NAME,
                    self::REQUEST_FIELD_NAME,
                    $this->getMock('Magento\Framework\Api\FilterBuilder', [], [], '', false),
                    $this->searchCriteriaBuilder,
                    $this->getMock('Magento\Framework\Reflection\DataObjectProcessor', [], [], '', false),
                    $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface'),
                    $this->providerMeta,
                    $this->providerData
                ]
            )
            ->getMockForAbstractClass();
    }

    /**
     * Testing of getName method
     */
    public function testGetName()
    {
        $this->assertEquals(self::DATA_PROVIDER_NAME, $this->dataProvider->getName());
    }

    /**
     * Testing of getConfigData method
     */
    public function testGetConfigData()
    {
        $this->assertEquals($this->providerConfig, $this->dataProvider->getConfigData());
    }

    /**
     * Testing of setConfigData method
     */
    public function testSetConfigData()
    {
        $newConfigData = ['configField' => 'newConfigValue'];
        $this->dataProvider->setConfigData($newConfigData);
        $this->assertEquals($newConfigData, $this->dataProvider->getConfigData());
    }

    /**
     * Testing of getMeta method
     */
    public function testGetMeta()
    {
        $this->assertEquals($this->providerMeta, $this->dataProvider->getMeta());
    }

    /**
     * Testing of getFieldMetaInfo method
     */
    public function testGetFieldMetaInfo()
    {
        $this->assertEquals(
            self::FIELD_META,
            $this->dataProvider->getFieldMetaInfo(self::FIELD_SET_NAME, self::FIELD_NAME)
        );
    }

    /**
     * Testing of getFieldSetMetaInfo method
     */
    public function testGetFieldSetMetaInfo()
    {
        $this->assertEquals(
            ['fields' => $this->providerFields],
            $this->dataProvider->getFieldSetMetaInfo(self::FIELD_SET_NAME)
        );
    }

    /**
     * Testing of getFieldsMetaInfo method
     */
    public function testGetFieldsMetaInfo()
    {
        $this->assertEquals(
            $this->providerFields,
            $this->dataProvider->getFieldsMetaInfo(self::FIELD_SET_NAME)
        );
    }

    /**
     * Testing of getPrimaryFieldName method
     */
    public function testGetPrimaryFieldName()
    {
        $this->assertEquals(self::PRIMARY_FIELD_NAME, $this->dataProvider->getPrimaryFieldName());
    }

    /**
     * Testing of getRequestFieldName method
     */
    public function testGetRequestFieldName()
    {
        $this->assertEquals(self::REQUEST_FIELD_NAME, $this->dataProvider->getRequestFieldName());
    }

    /**
     * Testing of addFilter method
     */
    public function testAddFilter()
    {
        /** @var \Magento\Framework\Api\Filter $filter */
        $filter = $this->getMock('Magento\Framework\Api\Filter', [], [], '', false);
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('addFilters')
            ->with($this->equalTo([$filter]));
        $this->dataProvider->addFilter($filter);
    }

    /**
     * Testing of addOrder method
     */
    public function testAddOrder()
    {
        $field = 'fieldName';
        $direction = 'ASC';
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('setSortOrders')
            ->with(
                $this->callback(
                    function ($sortOrders) use ($field, $direction) {
                        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
                        $sortOrder = $sortOrders[0];
                        return $sortOrder->getField() == $field && $sortOrder->getDirection() == $direction;
                    }
                )
            );
        $this->dataProvider->addOrder($field, $direction);
    }

    /**
     * Testing of setLimit method
     */
    public function testSetLimit()
    {
        $offset = 10;
        $size = 5;
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('setCurrentPage')
            ->with($this->equalTo($offset));
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('setPageSize')
            ->with($this->equalTo($size));
        $this->dataProvider->setLimit($offset, $size);
    }

    /**
     * Testing of getSearchCriteria method
     */
    public function testGetSearchCriteria()
    {
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->searchCriteria));
        $this->assertEquals($this->searchCriteria, $this->dataProvider->getSearchCriteria());
    }
}
