<?php
namespace Aheadworks\Blog\Test\Unit\Block\Adminhtml\Post\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Save
 */
class SaveTest extends \PHPUnit_Framework_TestCase
{
    const POST_ID = 1;

    /**
     * @var \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Save
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

        $postStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\PostInterface');
        $postRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\PostRepositoryInterface');
        $postRepositoryStub->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::POST_ID))
            ->will($this->returnValue($postStub));

        $this->button = $objectManager->getObject(
            'Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Save',
            [
                'request' => $requestStub,
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
}
