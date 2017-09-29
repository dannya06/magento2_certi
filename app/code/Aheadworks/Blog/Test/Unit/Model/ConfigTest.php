<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    const XML_PATH = 'xml/path/config';
    const VALUE = 'config value';

    /**
     * @var \Aheadworks\Blog\Model\Config
     */
    private $configModel;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfig;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->configModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Config',
            ['scopeConfig' => $this->scopeConfig]
        );
    }

    /**
     * Testing that scopes are defined correctly
     *
     * @dataProvider getValueScopesDataProvider
     */
    public function testGetValueScopes($path, $storeId, $websiteId, $expectedScopeType, $expectedScopeCode)
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                $this->equalTo($path),
                $this->equalTo($expectedScopeType),
                $this->equalTo($expectedScopeCode)
            );
        $this->configModel->getValue($path, $storeId, $websiteId);
    }

    /**
     * Testing return value of 'getValue' method
     */
    public function testGetValueResult()
    {
        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->willReturn(self::VALUE);
        $this->assertEquals(self::VALUE, $this->configModel->getValue(self::XML_PATH));
    }

    /**
     * @return array
     */
    public function getValueScopesDataProvider()
    {
        return [
            'store scope' => [self::XML_PATH, 1, null, ScopeInterface::SCOPE_STORE, 1],
            'website scope' => [self::XML_PATH, null, 2, ScopeInterface::SCOPE_WEBSITE, 2],
            'default scope' => [self::XML_PATH, null, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null]
        ];
    }
}
