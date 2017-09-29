<?php
namespace Aheadworks\Blog\Test\Unit\Ui\Component\Post\Form\Element;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Post\Form\Element\CommentsLink
 */
class CommentsLinkTest extends \PHPUnit_Framework_TestCase
{
    const DISQUS_ADMIN_URL = 'https://forum_code.disqus.com/admin/';

    /**
     * @var \Aheadworks\Blog\Ui\Component\Post\Form\Element\CommentsLink
     */
    private $commentsLink;

    /**
     * @var \Magento\Backend\Model\Auth\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authSession;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $processorStub = $this->getMock(
            'Magento\Framework\View\Element\UiComponent\Processor',
            ['register'],
            [],
            '',
            false
        );
        $contextStub = $this->getMockForAbstractClass('Magento\Framework\View\Element\UiComponent\ContextInterface');
        $contextStub->expects($this->any())
            ->method('getProcessor')
            ->will($this->returnValue($processorStub));

        $disqusStub = $this->getMock('Aheadworks\Blog\Model\Disqus', ['getAdminUrl'], [], '', false);
        $disqusStub->expects($this->any())
            ->method('getAdminUrl')
            ->will($this->returnValue(self::DISQUS_ADMIN_URL));

        $this->authSession = $this->getMock('Magento\Backend\Model\Auth\Session', ['isAllowed'], [], '', false);

        $this->commentsLink = $objectManager->getObject(
            'Aheadworks\Blog\Ui\Component\Post\Form\Element\CommentsLink',
            [
                'context' => $contextStub,
                'disqus' => $disqusStub,
                'authSession' => $this->authSession,
                'data' => ['config' => []]
            ]
        );
    }

    /**
     * Testing of prepare component configuration when comments management is allowed
     */
    public function testPrepareCommentsAllowed()
    {
        $this->authSession->expects($this->any())
            ->method('isAllowed')
            ->with($this->equalTo('Aheadworks_Blog::comments'))
            ->willReturn(true);
        $this->commentsLink->prepare();
        $config = $this->commentsLink->getData('config');
        $this->assertArrayHasKey('url', $config);
        $this->assertArrayHasKey('linkLabel', $config);
        $this->assertEquals(self::DISQUS_ADMIN_URL, $config['url']);
    }

    /**
     * Testing of prepare component configuration when comments management is not allowed
     */
    public function testPrepareCommentsIsNotAllowed()
    {
        $this->authSession->expects($this->any())
            ->method('isAllowed')
            ->with($this->equalTo('Aheadworks_Blog::comments'))
            ->willReturn(false);
        $this->commentsLink->prepare();
        $config = $this->commentsLink->getData('config');
        $this->assertArrayNotHasKey('url', $config);
        $this->assertArrayNotHasKey('linkLabel', $config);
    }
}
