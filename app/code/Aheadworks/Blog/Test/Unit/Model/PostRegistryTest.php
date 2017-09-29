<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\PostRegistry
 */
class PostRegistryTest extends \PHPUnit_Framework_TestCase
{
    const POST_ID = 1;
    const URL_KEY = 'post';

    /**
     * @var \Aheadworks\Blog\Model\PostRegistry
     */
    private $postRegistry;

    /**
     * @var \Aheadworks\Blog\Model\Post|\PHPUnit_Framework_MockObject_MockObject
     */
    private $post;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->post = $this->getMockBuilder('Aheadworks\Blog\Model\Post')
            ->setMethods(['load', 'loadByUrlKey', 'getId', 'getUrlKey'])
            ->disableOriginalConstructor()
            ->getMock();
        $postFactoryStab = $this->getMockBuilder('Aheadworks\Blog\Model\PostFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $postFactoryStab->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->post));
        $this->postRegistry = $objectManager->getObject(
            'Aheadworks\Blog\Model\PostRegistry',
            ['postFactory' => $postFactoryStab]
        );
    }

    /**
     * Testing that the Post Model is load during retrieving
     */
    public function testRetrieveLoadModel()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->post->expects($this->once())
            ->method('load')
            ->with(self::POST_ID)
            ->will($this->returnValue($this->post));
        $this->postRegistry->retrieve(self::POST_ID);
    }

    /**
     * Testing that the Post Model is cached during retrieving
     */
    public function testRetrieveCaching()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->postRegistry->retrieve(self::POST_ID);
        $this->post->expects($this->never())
            ->method('load')
            ->with(self::POST_ID)
            ->will($this->returnValue($this->post));
        $this->postRegistry->retrieve(self::POST_ID);
    }

    /**
     * Testing retrieve result
     */
    public function testRetrieveResult()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->post->expects($this->any())
            ->method('load')
            ->with(self::POST_ID)
            ->will($this->returnValue($this->post));
        $this->assertEquals(
            $this->post,
            $this->postRegistry->retrieve(self::POST_ID)
        );
    }

    /**
     * Testing exception while retrieving of non-existent Post Model
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRetrieveException()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(null));
        $this->post->expects($this->any())
            ->method('load')
            ->with(self::POST_ID)
            ->will($this->returnValue($this->post));
        $this->postRegistry->retrieve(self::POST_ID);
    }

    /**
     * Testing that the Post Model is load during retrieving by url-key
     */
    public function testRetrieveByUrlKeyLoadModel()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->post->expects($this->once())
            ->method('loadByUrlKey')
            ->with(self::URL_KEY)
            ->will($this->returnValue($this->post));
        $this->postRegistry->retrieveByUrlKey(self::URL_KEY);
    }

    /**
     * Testing that the Post Model is cached during retrieving by url-key
     */
    public function testRetrieveByUrlKeyCaching()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->postRegistry->retrieveByUrlKey(self::URL_KEY);
        $this->post->expects($this->never())
            ->method('loadByUrlKey')
            ->with(self::URL_KEY)
            ->will($this->returnValue($this->post));
        $this->postRegistry->retrieveByUrlKey(self::URL_KEY);
    }

    /**
     * Testing retrieve by url-key result
     */
    public function testRetrieveByUrlKeyResult()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->post->expects($this->any())
            ->method('loadByUrlKey')
            ->with(self::URL_KEY)
            ->will($this->returnValue($this->post));
        $this->assertEquals(
            $this->post,
            $this->postRegistry->retrieveByUrlKey(self::URL_KEY)
        );
    }

    /**
     * Testing exception while retrieving by url-key of non-existent Post Model
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRetrieveByUrlKeyException()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(null));
        $this->post->expects($this->any())
            ->method('loadByUrlKey')
            ->with(self::URL_KEY)
            ->will($this->returnValue($this->post));
        $this->postRegistry->retrieveByUrlKey(self::URL_KEY);
    }

    /**
     * Testing remove
     */
    public function testRemove()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->exactly(2))
            ->method('load')
            ->with(self::POST_ID)
            ->will($this->returnValue($this->post));
        $this->postRegistry->retrieve(self::POST_ID);
        $this->postRegistry->remove(self::POST_ID);
        $this->postRegistry->retrieve(self::POST_ID);
    }

    /**
     * Testing remove by url-key
     */
    public function testRemoveByUrlKey()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->post->expects($this->exactly(2))
            ->method('load')
            ->with(self::POST_ID)
            ->will($this->returnValue($this->post));
        $this->postRegistry->retrieve(self::POST_ID);
        $this->postRegistry->removeByUrlKey(self::URL_KEY);
        $this->postRegistry->retrieve(self::POST_ID);
    }

    /**
     * Test push
     */
    public function testPush()
    {
        $this->post->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $this->post->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->postRegistry->retrieve(self::POST_ID);
        /** @var \Aheadworks\Blog\Model\Post|\PHPUnit_Framework_MockObject_MockObject $newPost */
        $newPost = $this->getMockBuilder('Aheadworks\Blog\Model\Post')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getUrlKey'])
            ->getMock();
        $newPost->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::POST_ID));
        $newPost->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->postRegistry->push($newPost);
        $this->assertEquals($newPost, $this->postRegistry->retrieve(self::POST_ID));
    }
}
