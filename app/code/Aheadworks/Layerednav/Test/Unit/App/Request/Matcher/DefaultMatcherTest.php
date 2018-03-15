<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\App\Request\Matcher;

use Aheadworks\Layerednav\App\Request\AttributeList;
use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\App\Request\Matcher\DefaultMatcher;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\App\Request\Matcher\DefaultMatcher
 */
class DefaultMatcherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DefaultMatcher
     */
    private $matcher;

    /**
     * @var AttributeList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeListMock;

    /**
     * @var array
     */
    private $attributeCodes = ['attr1'];

    /**
     * @var array
     */
    private $decimalAttributeCodes = ['price', 'decimal'];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->attributeListMock = $this->getMockBuilder(AttributeList::class)
            ->setMethods(['getAttributeCodes'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->matcher = $objectManager->getObject(
            DefaultMatcher::class,
            [
                'attributeList' => $this->attributeListMock,
                'paramDataProvider' => $objectManager->getObject(ParamDataProvider::class)
            ]
        );
    }

    /**
     * @param array $params
     * @param bool $result
     * @dataProvider matchParamsDataProvider
     */
    public function testMatchParams($params, $result)
    {
        $requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn($params);
        $this->attributeListMock->expects($this->any())
            ->method('getAttributeCodes')
            ->willReturnMap(
                [
                    [AttributeList::LIST_TYPE_DEFAULT, $this->attributeCodes],
                    [AttributeList::LIST_TYPE_DECIMAL, $this->decimalAttributeCodes]
                ]
            );

        $class = new \ReflectionClass($this->matcher);
        $method = $class->getMethod('matchParams');
        $method->setAccessible(true);

        $this->assertEquals(
            $result,
            $method->invokeArgs($this->matcher, [$requestMock])
        );
    }

    /**
     * @return array
     */
    public function matchParamsDataProvider()
    {
        return [
            [['attr1' => '1'], true],
            [['attr1' => '1,2'], true],
            [['attr1' => 'value'], false],
            [['attr1' => '1,value'], false],
            [['price' => '10.00'], true],
            [['price' => '10.00-20.00'], true],
            [['cat' => '1'], true],
            [['cat' => '1,2'], true],
            [[], false],
            [['aw_new' => 1], true],
            [['aw_new' => 'new'], false],
            [[], false]
        ];
    }
}
