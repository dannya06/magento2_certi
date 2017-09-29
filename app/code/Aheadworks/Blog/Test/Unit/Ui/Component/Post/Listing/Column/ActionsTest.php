<?php
namespace Aheadworks\Blog\Test\Unit\Ui\Component\Post\Listing\Column;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Post\Listing\Column\Actions
 */
class ActionsTest extends \PHPUnit_Framework_TestCase
{
    const POST_ID = 1;
    const POST_TITLE = 'Post';

    const POST_EDIT_URL = 'http://localhost/admin/aw_blog_admin/post/edit/post_id/1';
    const POST_DELETE_URL = 'http://localhost/admin/aw_blog_admin/post/delete/post_id/1';

    const COLUMN_NAME = 'actions';

    /**
     * @var \Aheadworks\Blog\Ui\Component\Post\Listing\Column\Actions
     */
    private $column;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $post = [
        'id' => self::POST_ID,
        'title' => self::POST_TITLE
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

        $this->urlBuilder = $this->getMockForAbstractClass('Magento\Framework\UrlInterface');
        $this->urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will(
                $this->returnValueMap(
                    [
                        ['aw_blog_admin/post/edit', ['post_id' => self::POST_ID], self::POST_EDIT_URL],
                        ['aw_blog_admin/post/delete', ['post_id' => self::POST_ID], self::POST_DELETE_URL]
                    ]
                )
            );

        $this->column = $objectManager->getObject(
            'Aheadworks\Blog\Ui\Component\Post\Listing\Column\Actions',
            [
                'context' => $contextStub,
                'urlBuilder' => $this->urlBuilder,
                'data' => ['name' => self::COLUMN_NAME]
            ]
        );
    }

    /**
     * Testing of prepareDataSource method
     */
    public function testPrepareDataSource()
    {
        $dataSource = ['data' => ['items' => [$this->post]]];
        $dataSourcePrepared = $this->column->prepareDataSource($dataSource);
        $postItem = $dataSourcePrepared['data']['items'][0];
        $this->assertArrayHasKey(self::COLUMN_NAME, $postItem);
        $this->assertArrayHasKey('edit', $postItem[self::COLUMN_NAME]);
        $this->assertEquals(self::POST_EDIT_URL, $postItem[self::COLUMN_NAME]['edit']['href']);
        $this->assertArrayHasKey('delete', $postItem[self::COLUMN_NAME]);
        $this->assertEquals(self::POST_DELETE_URL, $postItem[self::COLUMN_NAME]['delete']['href']);
    }
}
