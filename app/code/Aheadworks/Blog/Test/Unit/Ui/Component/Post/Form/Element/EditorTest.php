<?php
namespace Aheadworks\Blog\Test\Unit\Ui\Component\Post\Form\Element;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Post\Form\Element\Editor
 */
class EditorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Ui\Component\Post\Form\Element\Editor
     */
    private $editor;

    /**
     * @var array
     */
    private $configData = ['wysiwygConfigField' => 'wysiwygConfigValue'];

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

        $wysiwygConfigStub = $this->getMock('Magento\Cms\Model\Wysiwyg\Config', ['getConfig'], [], '', false);
        $wysiwygConfigStub->expects($this->any())
            ->method('getConfig')
            ->will(
                $this->returnValue(new \Magento\Framework\DataObject($this->configData))
            );

        $this->editor = $objectManager->getObject(
            'Aheadworks\Blog\Ui\Component\Post\Form\Element\Editor',
            [
                'context' => $contextStub,
                'wysiwygConfig' => $wysiwygConfigStub,
                'data' => ['config' => []]
            ]
        );
    }

    /**
     * Testing of prepare component configuration
     */
    public function testPrepare()
    {
        $this->editor->prepare();
        $config = $this->editor->getData('config');
        $this->assertArrayHasKey('wysiwygConfig', $config);
        $this->assertEquals($this->configData, $config['wysiwygConfig']);
    }
}
