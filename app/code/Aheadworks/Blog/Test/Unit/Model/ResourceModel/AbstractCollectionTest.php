<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\AbstractCollection
 */
class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
{
    const STORE_ID = 1;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\AbstractCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractCollection;

    public function setUp()
    {
        $this->abstractCollection = $this->getMockForAbstractClass(
            'Aheadworks\Blog\Model\ResourceModel\AbstractCollection',
            [],
            '',
            false,
            false,
            true,
            ['addFilter']
        );
        $this->abstractCollection->expects($this->any())
            ->method('addFilter')
            ->will($this->returnSelf());
    }

    /**
     * Testing of adding store filter to collection
     *
     * @dataProvider addStoreFilterDataProvider
     */
    public function testAddStoreFilter($store, $withAdmin, $expectedValue)
    {
        $this->abstractCollection->expects($this->once())
            ->method('addFilter')
            ->with(
                $this->equalTo('store_id'),
                $this->equalTo($expectedValue),
                $this->equalTo('public')
            );
        $this->abstractCollection->addStoreFilter($store, $withAdmin);
    }

    /**
     * @return array
     */
    public function addStoreFilterDataProvider()
    {
        return [
            [self::STORE_ID, false, ['in' => [self::STORE_ID]]],
            [[self::STORE_ID], false, ['in' => [self::STORE_ID]]],
            [self::STORE_ID, true, ['in' => [self::STORE_ID, 0]]],
        ];
    }
}
