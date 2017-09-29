<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Adminhtml\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Adminhtml\Category\NewAction
 */
class NewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Controller\Adminhtml\Category\NewAction
     */
    private $action;

    /**
     * @var \Magento\Framework\Controller\Result\Forward|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultForward;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultForward = $this->getMock(
            'Magento\Framework\Controller\Result\Forward',
            ['forward'],
            [],
            '',
            false
        );
        $this->resultForward->expects($this->any())
            ->method('forward')
            ->will($this->returnSelf());
        $resultForwardFactoryStub = $this->getMock(
            'Magento\Backend\Model\View\Result\ForwardFactory',
            ['create'],
            [],
            '',
            false
        );
        $resultForwardFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultForward));

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Adminhtml\Category\NewAction',
            ['resultForwardFactory' => $resultForwardFactoryStub]
        );
    }

    /**
     * Testing of return value of execute method
     */
    public function testExecuteResult()
    {
        $this->assertSame($this->resultForward, $this->action->execute());
    }
}
