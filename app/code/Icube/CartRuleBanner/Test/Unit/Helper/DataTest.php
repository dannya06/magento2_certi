<?php

declare(strict_types=1);

namespace Icube\CartRuleBanner\Test\Unit\Helper;

use Icube\CartRuleBanner\Helper\Data;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Test class for Data Helper
 */
class DataTest extends TestCase
{
    protected $enable = true;

    protected $helperMock;
    protected $scopeConfigMock;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->scopeConfigMock->method('getValue')
            ->willReturn($this->enable);

        $data = [
            'scopeConfig' => $this->scopeConfigMock,
        ];

        $this->helperMock = $objectManager->getObject(Data::class, $data);
    }

    /**
     * Check Module Is enable or not
     *
     * @return void
     */
    public function testIsEnabled(): void
    {
        $this->assertEquals($this->enable, $this->helperMock->isEnabled());
    }
}
