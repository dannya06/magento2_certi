<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    const URL_KEY = 'cat';

    /**
     * @var \Aheadworks\Blog\Model\Post
     */
    private $postModel;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Post|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postResource;

    /**
     * Post model data
     *
     * @var array
     */
    private $postData = [
        'title' => 'Post',
        'url_key' => self::URL_KEY,
        'short_content' => 'Post short content',
        'content' => 'Post content',
        'is_allow_comments' => 1,
        'store_ids' => [1, 2],
        'categories' => [1, 2]
    ];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->postResource = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Post')
            ->setMethods(['getIdFieldName', 'load', 'isUrlKeyUnique'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->postModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Post',
            ['resource' => $this->postResource]
        );
    }

    /**
     * Testing that Post model data is loaded using resource model
     */
    public function testLoadByUrlKeyResourceLoad()
    {
        $this->postResource->expects($this->once())
            ->method('load')
            ->with(
                $this->equalTo($this->postModel),
                $this->equalTo(self::URL_KEY),
                $this->equalTo('url_key')
            );
        $this->postModel->loadByUrlKey(self::URL_KEY);
    }

    /**
     * Testing return value of 'loadByUrlKey' method
     */
    public function testLoadByUrlKeyResult()
    {
        $this->assertSame($this->postModel, $this->postModel->loadByUrlKey(self::URL_KEY));
    }

    /**
     * Testing that virtual status of Post model is defined properly
     *
     * @dataProvider getVirtualStatusDataProvider
     */
    public function testGetVirtualStatus($status, $publishDate, $virtualStatus)
    {
        $this->postModel
            ->setStatus($status)
            ->setPublishDate($publishDate);
        $this->assertEquals($virtualStatus, $this->postModel->getVirtualStatus());
    }

    /**
     * @return array
     */
    public function getVirtualStatusDataProvider()
    {
        $now = (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT);
        $tomorrow = (new \DateTime())->add(new \DateInterval('P1D'))->format(DateTime::DATETIME_PHP_FORMAT);
        return [
            'draft' => [Status::DRAFT, null, Status::DRAFT],
            'published' => [Status::PUBLICATION, $now, Status::PUBLICATION_PUBLISHED],
            'scheduled' => [Status::PUBLICATION, $tomorrow, Status::PUBLICATION_SCHEDULED],
        ];
    }

    /**
     * Testing that proper exceptions are thrown if post data is incorrect
     *
     * @dataProvider validateBeforeSaveDataProvider
     */
    public function testValidateBeforeSaveExceptions($postData, $exceptionMessage)
    {
        $this->postResource->expects($this->any())
            ->method('isUrlKeyUnique')
            ->willReturn(true);
        $this->postModel->setData($postData);
        try {
            $this->postModel->validateBeforeSave();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Magento\Framework\Validator\Exception', $e);
            $this->assertContains($exceptionMessage, $e->getMessage());
        }
    }

    /**
     * Testing that proper exception is thrown if post data contains duplicated URL-Key
     *
     * @expectedException \Magento\Framework\Validator\Exception
     * @expectedExceptionMessage This URL-Key is already assigned to another post or category.
     */
    public function testValidateBeforeSaveDuplicatedUrlKey()
    {
        $this->postResource->expects($this->any())
            ->method('isUrlKeyUnique')
            ->willReturn(false);
        $this->postModel->setData($this->postData);
        $this->postModel->validateBeforeSave();
    }

    /**
     * @return array
     */
    public function validateBeforeSaveDataProvider()
    {
        return [
            'empty title' => [
                array_merge($this->postData, ['title' => '']),
                'Title is required.'
            ],
            'empty URL-Key' => [
                array_merge($this->postData, ['url_key' => '']),
                'URL-Key is required.'
            ],
            'empty content' => [
                array_merge($this->postData, ['content' => '']),
                'Content is required.'
            ],
            'numeric URL-Key' => [
                array_merge($this->postData, ['url_key' => 123]),
                'URL-Key cannot consist only of numbers.'
            ],
            'invalid URL-Key' => [
                array_merge($this->postData, ['url_key' => 'invalid key*^']),
                'URL-Key cannot contain capital letters or disallowed symbols.'
            ],
            'empty stores' => [
                array_merge($this->postData, ['store_ids' => []]),
                'Select store view.'
            ]
        ];
    }
}
