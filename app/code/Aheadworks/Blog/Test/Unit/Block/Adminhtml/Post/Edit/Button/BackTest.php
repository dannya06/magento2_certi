<?php
namespace Aheadworks\Blog\Test\Unit\Block\Adminhtml\Post\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Back
 */
class BackTest extends \PHPUnit_Framework_TestCase
{
    const BACK_URL = 'http://localhost/blog_admin/post/index';

    /**
     * @var \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Back
     */
    private $button;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $urlBuilderStub = $this->getMockForAbstractClass('Magento\Framework\UrlInterface');
        $urlBuilderStub->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo('*/*/'))
            ->will($this->returnValue(self::BACK_URL));

        $this->button = $objectManager->getObject(
            'Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Back',
            ['urlBuilder' => $urlBuilderStub]
        );
    }

    /**
     * Testing of return value of getButtonData method
     */
    public function testGetButtonData()
    {
        $this->assertTrue(is_array($this->button->getButtonData()));
    }

    /**
     * Testing of retrieving of back url
     */
    public function testGetBackUrl()
    {
        $this->assertEquals(self::BACK_URL, $this->button->getBackUrl());
    }
}
