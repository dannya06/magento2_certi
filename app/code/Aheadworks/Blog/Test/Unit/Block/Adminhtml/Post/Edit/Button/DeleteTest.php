<?php
namespace Aheadworks\Blog\Test\Unit\Block\Adminhtml\Post\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Delete
 */
class DeleteTest extends \PHPUnit_Framework_TestCase
{
    const DELETE_URL = 'http://localhost/blog_admin/post/delete/post_id/1';
    const POST_ID = 1;

    /**
     * @var \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Delete
     */
    private $button;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $requestStub = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $requestStub->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('post_id'))
            ->will($this->returnValue(self::POST_ID));
        $urlBuilderStub = $this->getMockForAbstractClass('Magento\Framework\UrlInterface');
        $urlBuilderStub->expects($this->any())
            ->method('getUrl')
            ->with(
                $this->equalTo('*/*/delete'),
                $this->equalTo(['post_id' => self::POST_ID])
            )
            ->will($this->returnValue(self::DELETE_URL));

        $postStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $postRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface');
        $postRepositoryStub->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::POST_ID))
            ->will($this->returnValue($postStub));

        $this->button = $objectManager->getObject(
            'Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Delete',
            [
                'request' => $requestStub,
                'urlBuilder' => $urlBuilderStub,
                'postRepository' => $postRepositoryStub
            ]
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
     * Testing of retrieving of delete url
     */
    public function testGetDeleteUrl()
    {
        $this->assertEquals(self::DELETE_URL, $this->button->getDeleteUrl());
    }
}
