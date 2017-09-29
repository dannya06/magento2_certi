<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Adminhtml\Comment;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Adminhtml\Comment\Index
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    const DISQUS_ADMIN_URL = 'https://forum_code.disqus.com/admin/';

    /**
     * @var \Aheadworks\Blog\Controller\Adminhtml\Comment\Index
     */
    private $action;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirect;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirect = $this->getMock(
            'Magento\Framework\Controller\Result\Redirect',
            ['setUrl'],
            [],
            '',
            false
        );
        $this->resultRedirect->expects($this->any())
            ->method('setUrl')
            ->will($this->returnSelf());
        $resultRedirectFactoryStub = $this->getMock(
            'Magento\Backend\Model\View\Result\RedirectFactory',
            ['create'],
            [],
            '',
            false
        );
        $resultRedirectFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultRedirect));

        $disqusStub = $this->getMock('Aheadworks\Blog\Model\Disqus', ['getAdminUrl'], [], '', false);
        $disqusStub->expects($this->any())
            ->method('getAdminUrl')
            ->will($this->returnValue(self::DISQUS_ADMIN_URL));

        $context = $objectManager->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'resultRedirectFactory' => $resultRedirectFactoryStub
            ]
        );

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Adminhtml\Comment\Index',
            [
                'context' => $context,
                'disqus' => $disqusStub
            ]
        );
    }

    /**
     * Testing of return value of execute method
     */
    public function testExecuteResult()
    {
        $this->resultRedirect->expects($this->atLeastOnce())
            ->method('setUrl')
            ->with($this->equalTo(self::DISQUS_ADMIN_URL));
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }
}
