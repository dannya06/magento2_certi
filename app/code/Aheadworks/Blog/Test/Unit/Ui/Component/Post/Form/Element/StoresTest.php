<?php
namespace Aheadworks\Blog\Test\Unit\Ui\Component\Post\Form\Element;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Post\Form\Element\Stores
 */
class StoresTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Ui\Component\Post\Form\Element\Stores
     */
    private $stores;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

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

        $this->storeManager = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $storeOptionsStub = $this->getMock('Magento\Store\Model\System\Store', ['toOptionArray'], [], '', false);
        $storeOptionsStub->expects($this->any())
            ->method('toOptionArray')
            ->will(
                $this->returnValue([['value' => 'optionValue', 'label' => 'optionLabel']])
            );

        $this->stores = $objectManager->getObject(
            'Aheadworks\Blog\Ui\Component\Post\Form\Element\Stores',
            [
                'context' => $contextStub,
                'storeManager' => $this->storeManager,
                'storeOptions' => $storeOptionsStub,
                'data' => ['config' => []]
            ]
        );
    }

    /**
     * Testing of prepare component configuration if single store mode
     */
    public function testPrepareSingleStore()
    {
        $this->storeManager->expects($this->any())
            ->method('hasSingleStore')
            ->willReturn(true);
        $this->stores->prepare();
        $config = $this->stores->getData('config');
        $this->assertArrayHasKey('visible', $config);
        $this->assertFalse($config['visible']);
    }

    /**
     * Testing of prepare component configuration if multi store mode
     */
    public function testPrepareMultiStore()
    {
        $this->storeManager->expects($this->any())
            ->method('hasSingleStore')
            ->willReturn(false);
        $this->stores->prepare();
        $config = $this->stores->getData('config');
        $this->assertArrayNotHasKey('visible', $config);
    }
}
