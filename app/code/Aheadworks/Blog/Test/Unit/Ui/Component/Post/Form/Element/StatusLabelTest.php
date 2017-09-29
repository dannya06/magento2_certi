<?php
namespace Aheadworks\Blog\Test\Unit\Ui\Component\Post\Form\Element;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Post\Form\Element\StatusLabel
 */
class StatusLabelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Ui\Component\Post\Form\Element\StatusLabel
     */
    private $statusLabel;

    /**
     * @var array
     */
    private $sourceArray = ['optionName' => 'optionValue'];

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

        $statusSourceStub = $this->getMock('Aheadworks\Blog\Model\Source\Post\Status', ['getOptions'], [], '', false);
        $statusSourceStub->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($this->sourceArray));

        $this->statusLabel = $objectManager->getObject(
            'Aheadworks\Blog\Ui\Component\Post\Form\Element\StatusLabel',
            [
                'context' => $contextStub,
                'statusSource' => $statusSourceStub,
                'data' => ['config' => []]
            ]
        );
    }

    /**
     * Testing of prepare component configuration
     */
    public function testPrepare()
    {
        $this->statusLabel->prepare();
        $config = $this->statusLabel->getData('config');
        $this->assertArrayHasKey('statusOptions', $config);
        $this->assertEquals($this->sourceArray, $config['statusOptions']);
    }
}
