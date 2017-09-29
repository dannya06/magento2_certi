<?php
namespace Aheadworks\Blog\Test\Unit\Model\Config\Backend\Blog;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Config\Backend\Blog\Route
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\Config\Backend\Blog\Route
     */
    private $configModel;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->configModel = $objectManager->getObject('Aheadworks\Blog\Model\Config\Backend\Blog\Route');
    }

    /**
     * Testing of value filtering before save
     */
    public function testBeforeSave()
    {
        $value = '  blog  ';
        $filteredValue = 'blog';
        $this->configModel->setValue($value);
        $this->configModel->beforeSave();
        $this->assertEquals($filteredValue, $this->configModel->getValue());
    }
}
