<?php
namespace Aheadworks\Blog\Test\Unit\Block\Adminhtml\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Adminhtml\Category\Edit
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \Magento\Backend\Block\Widget\Button\ButtonList
     */
    private $buttonList;

    /**
     * @var \Magento\Backend\Block\Widget\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $buttonItemStub = $this->getMock('Magento\Backend\Block\Widget\Button\Item', ['getSortOrder'], [], '', false);
        $buttonItemStub->expects($this->any())
            ->method('getSortOrder')
            ->will($this->returnValue(0));
        $buttonItemFactoryStub = $this->getMock(
            'Magento\Backend\Block\Widget\Button\ItemFactory',
            ['create'],
            [],
            '',
            false
        );
        $buttonItemFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($buttonItemStub));

        $this->buttonList = $this->objectManager->getObject(
            'Magento\Backend\Block\Widget\Button\ButtonList',
            ['itemFactory' => $buttonItemFactoryStub]
        );
        $this->context = $this->objectManager->getObject(
            'Magento\Backend\Block\Widget\Context',
            ['buttonList' => $this->buttonList]
        );
    }

    /**
     * Testing of block creation
     */
    public function testCreate()
    {
        $this->objectManager->getObject(
            'Aheadworks\Blog\Block\Adminhtml\Category\Edit',
            ['context' => $this->context]
        );
        $buttons = $this->buttonList->getItems();
        $this->assertArrayNotHasKey('reset', $buttons[-1]);
        $this->assertArrayHasKey('saveandcontinue', $buttons[-1]);
    }
}
