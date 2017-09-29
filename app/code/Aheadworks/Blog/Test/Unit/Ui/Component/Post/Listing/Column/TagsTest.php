<?php
namespace Aheadworks\Blog\Test\Unit\Ui\Component\Post\Listing\Column;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Post\Listing\Column\Tags
 */
class TagsTest extends \PHPUnit_Framework_TestCase
{
    const TAG1_NAME = 'tag 1';
    const TAG2_NAME = 'tag 2';

    /**
     * @var \Aheadworks\Blog\Ui\Component\Post\Listing\Column\Tags
     */
    private $column;

    /**
     * @var array
     */
    private $post = [
        'tags' => [self::TAG1_NAME, self::TAG2_NAME]
    ];

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

        $this->column = $objectManager->getObject(
            'Aheadworks\Blog\Ui\Component\Post\Listing\Column\Tags',
            ['context' => $contextStub]
        );
    }

    /**
     * Testing of prepareDataSource method
     */
    public function testPrepareDataSource()
    {
        $dataSource = ['data' => ['items' => [$this->post]]];
        $dataSourcePrepared = $this->column->prepareDataSource($dataSource);
        $this->assertEquals(
            self::TAG1_NAME . ', ' . self::TAG2_NAME,
            $dataSourcePrepared['data']['items'][0]['tags']
        );
    }
}
