<?php
namespace Aheadworks\Blog\Test\Unit\Block\Adminhtml\Post\Edit;

/**
 * Test for \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button
 */
class ButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button|\PHPUnit_Framework_MockObject_MockObject
     */
    private $button;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilder;

    public function setUp()
    {
        $this->urlBuilder = $this->getMockForAbstractClass('Magento\Framework\UrlInterface');
        $this->button = $this->getMockForAbstractClass(
            'Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button',
            [
                $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface'),
                $this->urlBuilder,
                $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface')
            ]
        );
    }

    /**
     * Testing of retrieving url
     */
    public function testGetUrl()
    {
        $route = '*/*/*';
        $params = ['paramName' => 'paramValue'];
        $url = 'http://localhost/blog_admin/paramName/paramValue';
        $this->urlBuilder->expects($this->atLeastOnce())
            ->method('getUrl')
            ->with(
                $this->equalTo($route),
                $this->equalTo($params)
            )
            ->willReturn($url);
        $this->assertEquals($url, $this->button->getUrl($route, $params));
    }
}
