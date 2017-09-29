<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Aheadworks\Blog\Model\Source\Category\Status;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Category
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    const URL_KEY = 'cat';

    /**
     * @var \Aheadworks\Blog\Model\Category
     */
    private $categoryModel;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryResource;

    /**
     * Category model data
     *
     * @var array
     */
    private $categoryData = [
        'name' => 'Category',
        'url_key' => self::URL_KEY,
        'status' => Status::ENABLED,
        'sort_order' => 0,
        'store_ids' => [1, 2]
    ];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->categoryResource = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Category')
            ->setMethods(['getIdFieldName', 'load', 'isUrlKeyUnique'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Category',
            ['resource' => $this->categoryResource]
        );
    }

    /**
     * Testing that Category model data is loaded using resource model
     */
    public function testLoadByUrlKeyResourceLoad()
    {
        $this->categoryResource->expects($this->once())
            ->method('load')
            ->with(
                $this->equalTo($this->categoryModel),
                $this->equalTo(self::URL_KEY),
                $this->equalTo('url_key')
            );
        $this->categoryModel->loadByUrlKey(self::URL_KEY);
    }

    /**
     * Testing return value of 'loadByUrlKey' method
     */
    public function testLoadByUrlKeyResult()
    {
        $this->assertSame($this->categoryModel, $this->categoryModel->loadByUrlKey(self::URL_KEY));
    }

    /**
     * Testing that proper exceptions are thrown if category data is incorrect
     *
     * @dataProvider validateBeforeSaveDataProvider
     */
    public function testValidateBeforeSaveExceptions($categoryData, $exceptionMessage)
    {
        $this->categoryResource->expects($this->any())
            ->method('isUrlKeyUnique')
            ->willReturn(true);
        $this->categoryModel->setData($categoryData);
        try {
            $this->categoryModel->validateBeforeSave();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Magento\Framework\Validator\Exception', $e);
            $this->assertContains($exceptionMessage, $e->getMessage());
        }
    }

    /**
     * Testing that proper exception is thrown if category data contains duplicated URL-Key
     *
     * @expectedException \Magento\Framework\Validator\Exception
     * @expectedExceptionMessage This URL-Key is already assigned to another post or category.
     */
    public function testValidateBeforeSaveDuplicatedUrlKey()
    {
        $this->categoryResource->expects($this->any())
            ->method('isUrlKeyUnique')
            ->willReturn(false);
        $this->categoryModel->setData($this->categoryData);
        $this->categoryModel->validateBeforeSave();
    }

    /**
     * @return array
     */
    public function validateBeforeSaveDataProvider()
    {
        return [
            'empty name' => [
                array_merge($this->categoryData, ['name' => '']),
                'Name is required.'
            ],
            'empty URL-Key' => [
                array_merge($this->categoryData, ['url_key' => '']),
                'URL-Key is required.'
            ],
            'numeric URL-Key' => [
                array_merge($this->categoryData, ['url_key' => 123]),
                'URL-Key cannot consist only of numbers.'
            ],
            'invalid URL-Key' => [
                array_merge($this->categoryData, ['url_key' => 'invalid key*^']),
                'URL-Key cannot contain capital letters or disallowed symbols.'
            ],
            'empty stores' => [
                array_merge($this->categoryData, ['store_ids' => []]),
                'Select store view.'
            ]
        ];
    }
}
