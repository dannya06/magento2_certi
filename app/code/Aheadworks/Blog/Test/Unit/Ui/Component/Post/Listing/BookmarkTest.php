<?php
namespace Aheadworks\Blog\Test\Unit\Ui\Component\Post\Listing;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Post\Listing\Bookmark
 */
class BookmarkTest extends \PHPUnit_Framework_TestCase
{
    const VIEW_INDEX = 'view_index';
    const VIEW_TITLE = 'View';

    const USER_ID = 1;

    /**
     * @var array
     */
    private $changeColumns = ['title' => ['sorting' => 'asc']];

    /**
     * @var array
     */
    private $filters = ['title' => ['like' => 'Vi']];

    /**
     * @var \Aheadworks\Blog\Ui\Component\Post\Listing\Bookmark
     */
    private $bookmarkComponent;

    /**
     * @var \Magento\Ui\Api\Data\BookmarkInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bookmark;

    /**
     * @var \Magento\Ui\Api\BookmarkRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bookmarkRepository;

    /**
     * @var \Magento\Framework\View\Element\UiComponent\ContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextStub;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $processorStub = $this->getMock(
            'Magento\Framework\View\Element\UiComponent\Processor',
            ['register'],
            [],
            '',
            false
        );
        $this->contextStub = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\UiComponent\ContextInterface'
        );
        $this->contextStub->expects($this->any())
            ->method('getProcessor')
            ->will($this->returnValue($processorStub));

        $this->bookmark = $this->getMockForAbstractClass('Magento\Ui\Api\Data\BookmarkInterface');
        $this->bookmark->expects($this->any())
            ->method('setUserId')
            ->will($this->returnSelf());
        $this->bookmark->expects($this->any())
            ->method('setNamespace')
            ->will($this->returnSelf());
        $this->bookmark->expects($this->any())
            ->method('setIdentifier')
            ->will($this->returnSelf());
        $this->bookmark->expects($this->any())
            ->method('setTitle')
            ->will($this->returnSelf());
        $this->bookmark->expects($this->any())
            ->method('setConfig')
            ->will($this->returnSelf());

        $bookmarkFactoryStub = $this->getMock(
            'Magento\Ui\Api\Data\BookmarkInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $bookmarkFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->bookmark));

        $userContextStub = $this->getMockForAbstractClass('Magento\Authorization\Model\UserContextInterface');
        $userContextStub->expects($this->any())
            ->method('getUserId')
            ->will($this->returnValue(self::USER_ID));

        $this->bookmarkRepository = $this->getMockForAbstractClass('Magento\Ui\Api\BookmarkRepositoryInterface');

        $this->bookmarkComponent = $this->objectManager->getObject(
            'Aheadworks\Blog\Ui\Component\Post\Listing\Bookmark',
            [
                'bookmarkFactory' => $bookmarkFactoryStub,
                'userContext' => $userContextStub,
                'context' => $this->contextStub,
                'bookmarkRepository' => $this->bookmarkRepository,
                'data' => ['config' => []]
            ]
        );
    }

    /**
     * Testing of prepare component configuration
     */
    public function testPrepare()
    {
        $bookmarkMock = $this->getMock(
            'Aheadworks\Blog\Ui\Component\Post\Listing\Bookmark',
            ['addView'],
            $this->objectManager->getConstructArguments(
                'Aheadworks\Blog\Ui\Component\Post\Listing\Bookmark',
                ['context' => $this->contextStub]
            ),
            '',
            true
        );
        $bookmarkMock->expects($this->exactly(4))
            ->method('addView')
            ->withConsecutive(
                [$this->equalTo('default')],
                [$this->equalTo('drafts')],
                [$this->equalTo('scheduled')],
                [$this->equalTo('new_comments')]
            );
        $bookmarkMock->prepare();
    }

    /**
     * Testing of adding view to the current config
     */
    public function testAddViewToConfig()
    {
        $this->bookmarkComponent->addView(
            self::VIEW_INDEX,
            self::VIEW_TITLE,
            $this->changeColumns,
            $this->filters
        );
        $config = $this->bookmarkComponent->getData('config');
        $this->assertArrayHasKey('views', $config);
        $this->assertArrayHasKey(self::VIEW_INDEX, $config['views']);
        $this->assertArrayHasKey('index', $config['views'][self::VIEW_INDEX]);
        $this->assertArrayHasKey('label', $config['views'][self::VIEW_INDEX]);
        $this->assertEquals(self::VIEW_INDEX, $config['views'][self::VIEW_INDEX]['index']);
        $this->assertEquals(self::VIEW_TITLE, $config['views'][self::VIEW_INDEX]['label']);
        $this->assertEquals(
            'asc',
            $config['views'][self::VIEW_INDEX]['data']['columns']['title']['sorting']
        );
        $this->assertArrayHasKey(
            'title',
            $config['views'][self::VIEW_INDEX]['data']['filters']['applied']
        );
        $this->assertEquals(
            ['like' => 'Vi'],
            $config['views'][self::VIEW_INDEX]['data']['filters']['applied']['title']
        );
    }

    /**
     * Testing that the added view is saved
     */
    public function testAddViewSave()
    {
        $this->bookmark->expects($this->atLeastOnce())
            ->method('setUserId')
            ->with($this->equalTo(self::USER_ID));
        $this->bookmark->expects($this->atLeastOnce())
            ->method('setIdentifier')
            ->with($this->equalTo(self::VIEW_INDEX));
        $this->bookmark->expects($this->atLeastOnce())
            ->method('setTitle')
            ->with($this->equalTo(self::VIEW_TITLE));
        $this->bookmarkRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($this->bookmark));
        $this->bookmarkComponent->addView(
            self::VIEW_INDEX,
            self::VIEW_TITLE,
            $this->changeColumns,
            $this->filters
        );
    }

    /**
     * Testing return value of getDefaultViewConfig method
     */
    public function testGetDefaultViewConfigResult()
    {
        $this->assertTrue(is_array($this->bookmarkComponent->getDefaultViewConfig()));
    }
}
