<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Tag
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    const NAME = 'tag';
    const POST_ID = 1;

    /**
     * @var \Aheadworks\Blog\Model\Tag
     */
    private $tagModel;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagResource;

    /**
     * Tag model data
     *
     * @var array
     */
    private $tagData = ['name' => self::NAME];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->tagResource = $this->getMockBuilder('Aheadworks\Blog\Model\ResourceModel\Tag')
            ->setMethods(['getIdFieldName', 'load', 'isNameUnique'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Tag',
            ['resource' => $this->tagResource]
        );
    }

    /**
     * Testing that Tag model data is loaded using resource model
     */
    public function testLoadByNameResourceLoad()
    {
        $this->tagResource->expects($this->once())
            ->method('load')
            ->with(
                $this->equalTo($this->tagModel),
                $this->equalTo(self::NAME),
                $this->equalTo('name')
            );
        $this->tagModel->loadByName(self::NAME);
    }

    /**
     * Testing return value of 'loadByName' method
     */
    public function testLoadByUrlKeyResult()
    {
        $this->assertSame($this->tagModel, $this->tagModel->loadByName(self::NAME));
    }

    /**
     * Testing return value of 'getPosts' method
     */
    public function testGetPostsResult()
    {
        $posts = [self::POST_ID];
        $this->tagModel->setData('posts', $posts);
        $this->assertEquals($posts, $this->tagModel->getPosts());
    }

    /**
     * Testing that post id is added in the Tag
     */
    public function testAddPostAdded()
    {
        $this->tagModel->addPost(self::POST_ID);
        $this->assertTrue(in_array(self::POST_ID, $this->tagModel->getPosts()));
    }

    /**
     * Testing that post id is not duplicated in the 'posts' data array
     */
    public function testAddPostNotDuplicated()
    {
        $this->tagModel->addPost(self::POST_ID);
        $this->tagModel->addPost(self::POST_ID);
        $this->assertEquals([self::POST_ID], $this->tagModel->getPosts());
    }

    /**
     * Testing return value of 'addPost' method
     */
    public function testAddPostResult()
    {
        $this->assertSame($this->tagModel, $this->tagModel->addPost(self::POST_ID));
    }

    /**
     * Testing that post id is removed from the Tag
     */
    public function testRemovePostRemoved()
    {
        $posts = [self::POST_ID];
        $this->tagModel->setData('posts', $posts);
        $this->tagModel->removePost(self::POST_ID);
        $this->assertEmpty($this->tagModel->getPosts());
    }

    /**
     * Testing return value of 'removePost' method
     */
    public function testRemovePostResult()
    {
        $this->assertSame($this->tagModel, $this->tagModel->removePost(self::POST_ID));
    }

    /**
     * Testing that proper exceptions are thrown if tag data is incorrect
     *
     * @dataProvider validateBeforeSaveDataProvider
     */
    public function testValidateBeforeSaveExceptions($tagData, $exceptionMessage)
    {
        $this->tagResource->expects($this->any())
            ->method('isNameUnique')
            ->willReturn(true);
        $this->tagModel->setData($tagData);
        try {
            $this->tagModel->validateBeforeSave();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Magento\Framework\Validator\Exception', $e);
            $this->assertContains($exceptionMessage, $e->getMessage());
        }
    }

    /**
     * Testing that proper exception is thrown if tag data contains duplicated name
     *
     * @expectedException \Magento\Framework\Validator\Exception
     * @expectedExceptionMessage Tag name already exist.
     */
    public function testValidateBeforeSaveDuplicatedName()
    {
        $this->tagResource->expects($this->any())
            ->method('isNameUnique')
            ->willReturn(false);
        $this->tagModel->setData($this->tagData);
        $this->tagModel->validateBeforeSave();
    }

    /**
     * @return array
     */
    public function validateBeforeSaveDataProvider()
    {
        return [
            'empty name' => [
                array_merge($this->tagData, ['name' => '']),
                'Empty tags are not allowed.'
            ]
        ];
    }
}
