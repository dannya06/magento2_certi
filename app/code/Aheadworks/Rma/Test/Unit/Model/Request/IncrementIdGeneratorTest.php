<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Test\Unit\Model\Request;

use Aheadworks\Rma\Model\Request\IncrementIdGenerator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\DB\Adapter\AdapterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\ResourceConnection;

/**
 * Class IncrementIdGeneratorTest
 * Test for \Aheadworks\Rma\Model\Request\IncrementIdGenerator
 *
 * @package Aheadworks\Rma\Test\Unit\Model\Request
 */
class IncrementIdGeneratorTest extends TestCase
{
    /**
     * @var IncrementIdGenerator
     */
    private $model;

    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->connectionMock = $this->getMockForAbstractClass(AdapterInterface::class);
        $resourceConnectionMock = $this->getMockBuilder(ResourceConnection::class)
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $resourceConnectionMock->expects($this->atLeastOnce())
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->model = $objectManager->getObject(
            IncrementIdGenerator::class,
            [
                'resourceConnection' => $resourceConnectionMock
            ]
        );
    }

    /**
     * Test generate method
     */
    public function testGenerate()
    {
        $entityStatus['Auto_increment'] = 1;
        $this->connectionMock->expects($this->once())
            ->method('showTableStatus')
            ->with('aw_rma_request')
            ->willReturn($entityStatus);

        $this->assertEquals($entityStatus['Auto_increment'], $this->model->generate());
    }

    /**
     * Test generate method on exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Cannot get autoincrement value
     */
    public function testGenerateOnException()
    {
        $entityStatus['Auto_increment'] = null;
        $this->connectionMock->expects($this->once())
            ->method('showTableStatus')
            ->with('aw_rma_request')
            ->willReturn($entityStatus);

        $this->model->generate();
    }
}
