<?php
namespace Aheadworks\Blog\Test\Unit\Block\Adminhtml\Post\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\SaveAsDraft
 */
class SaveAsDraftTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\SaveAsDraft
     */
    private $button;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->button = $objectManager->getObject('Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\SaveAsDraft');
    }

    /**
     * Testing of return value of getButtonData method
     */
    public function testGetButtonData()
    {
        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}
