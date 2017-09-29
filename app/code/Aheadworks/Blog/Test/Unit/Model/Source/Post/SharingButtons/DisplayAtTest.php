<?php
namespace Aheadworks\Blog\Test\Unit\Model\Source\Post\SharingButtons;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Source\Post\SharingButtons\DisplayAt
 */
class DisplayAtTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\Source\Post\SharingButtons\DisplayAt
     */
    private $sourceModel;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->sourceModel = $objectManager->getObject('Aheadworks\Blog\Model\Source\Post\SharingButtons\DisplayAt');
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->sourceModel->toOptionArray()));
    }
}
