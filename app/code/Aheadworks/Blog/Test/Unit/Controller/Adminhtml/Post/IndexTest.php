<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Adminhtml\Post;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Adminhtml\Post\Index
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Controller\Adminhtml\Post\Index
     */
    private $action;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPage;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $titleStub = $this->getMock('Magento\Framework\View\Page\Title', ['prepend'], [], '', false);
        $pageConfigStub = $this->getMock('Magento\Framework\View\Page\Config', ['getTitle'], [], '', false);
        $pageConfigStub->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue($titleStub));
        $this->resultPage = $this->getMock(
            'Magento\Backend\Model\View\Result\Page',
            ['setActiveMenu', 'getConfig'],
            [],
            '',
            false
        );
        $this->resultPage->expects($this->any())
            ->method('setActiveMenu')
            ->will($this->returnSelf());
        $this->resultPage->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($pageConfigStub));
        $resultPageFactoryStub = $this->getMock('Magento\Framework\View\Result\PageFactory', ['create'], [], '', false);
        $resultPageFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultPage));

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Adminhtml\Post\Index',
            ['resultPageFactory' => $resultPageFactoryStub]
        );
    }

    /**
     * Testing of return value of execute method
     */
    public function testExecuteResult()
    {
        $this->assertSame($this->resultPage, $this->action->execute());
    }
}
