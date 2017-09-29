<?php
namespace Aheadworks\Blog\Test\Unit\Ui\Component\Category\Listing\Column;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Category\Listing\Column\Actions
 */
class ActionsTest extends \PHPUnit_Framework_TestCase
{
    const CATEGORY_ID = 1;
    const CATEGORY_NAME = 'Category';

    const CATEGORY_EDIT_URL = 'http://localhost/admin/aw_blog_admin/category/edit/cat_id/1';
    const CATEGORY_DELETE_URL = 'http://localhost/admin/aw_blog_admin/category/delete/cat_id/1';

    const COLUMN_NAME = 'actions';

    /**
     * @var \Aheadworks\Blog\Ui\Component\Category\Listing\Column\Actions
     */
    private $column;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $category = [
        'id' => self::CATEGORY_ID,
        'name' => self::CATEGORY_NAME
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
                        ['aw_blog_admin/category/edit', ['cat_id' => self::CATEGORY_ID], self::CATEGORY_EDIT_URL],
                        ['aw_blog_admin/category/delete', ['cat_id' => self::CATEGORY_ID], self::CATEGORY_DELETE_URL]
                    ]
                )
            );

        $this->column = $objectManager->getObject(
            'Aheadworks\Blog\Ui\Component\Category\Listing\Column\Actions',
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
        $dataSource = ['data' => ['items' => [$this->category]]];
        $dataSourcePrepared = $this->column->prepareDataSource($dataSource);
        $categoryItem = $dataSourcePrepared['data']['items'][0];
        $this->assertArrayHasKey(self::COLUMN_NAME, $categoryItem);
        $this->assertArrayHasKey('edit', $categoryItem[self::COLUMN_NAME]);
        $this->assertEquals(self::CATEGORY_EDIT_URL, $categoryItem[self::COLUMN_NAME]['edit']['href']);
        $this->assertArrayHasKey('delete', $categoryItem[self::COLUMN_NAME]);
        $this->assertEquals(self::CATEGORY_DELETE_URL, $categoryItem[self::COLUMN_NAME]['delete']['href']);
    }
}
