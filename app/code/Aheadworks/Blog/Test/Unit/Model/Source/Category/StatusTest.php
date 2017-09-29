<?php
namespace Aheadworks\Blog\Test\Unit\Model\Source\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Source\Category\Status
 */
class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\Source\Category\Status
     */
    private $sourceModel;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->sourceModel = $objectManager->getObject('Aheadworks\Blog\Model\Source\Category\Status');
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->sourceModel->toOptionArray()));
    }
}
