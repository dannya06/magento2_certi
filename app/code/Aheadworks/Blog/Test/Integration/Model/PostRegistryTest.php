<?php
namespace Aheadworks\Blog\Test\Integration\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Aheadworks\Blog\Model\PostRegistry
 */
class PostRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\PostRegistry
     */
    private $registry;

    /**
     * @var int|null
     */
    private $postId;

    protected function setUp()
    {
        $this->registry = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\PostRegistry');
        /** @var \Aheadworks\Blog\Model\Post $fixturePost */
        $fixturePost = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Post')
            ->load('fixturepost', 'url_key');
        if ($fixturePost->getId()) {
            $this->postId = $fixturePost->getId();
        }
    }

    /**
     * Test of retrieving post instance
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testRetrieve()
    {
        $post = $this->registry->retrieve($this->postId);
        $this->assertInstanceOf('Aheadworks\Blog\Model\Post', $post);
        $this->assertEquals($this->postId, $post->getId());
    }

    /**
     * Test of retrieving post instance by url key
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testRetrieveByUrlKey()
    {
        $post = $this->registry->retrieveByUrlKey('fixturepost');
        $this->assertInstanceOf('Aheadworks\Blog\Model\Post', $post);
        $this->assertEquals('fixturepost', $post->getUrlKey());
    }

    /**
     * Test of caching of post instance
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testRetrieveCached()
    {
        $post = $this->registry->retrieve($this->postId);
        Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Post')
            ->load($this->postId)
            ->delete();
        $this->assertEquals($post, $this->registry->retrieve($this->postId));
        $this->assertEquals($post, $this->registry->retrieveByUrlKey('fixturepost'));
    }

    /**
     * Testing that exception is thrown while retrieving of nonexistent post instance
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with postId = 333
     */
    public function testRetrieveException()
    {
        $postId = 333;
        $this->registry->retrieve($postId);
    }

    /**
     * Testing that exception is thrown while retrieving by url key of nonexistent post instance
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with urlKey = postnotexists
     */
    public function testRetrieveByUrlKeyException()
    {
        $postUrlKey = 'postnotexists';
        $this->registry->retrieveByUrlKey($postUrlKey);
    }

    /**
     * Test of removing post instance
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemove()
    {
        $post = $this->registry->retrieve($this->postId);
        $post->delete();
        $this->registry->remove($this->postId);
        $this->registry->retrieve($this->postId);
    }

    /**
     * Test of removing post instance by url key
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemoveByUrlKey()
    {
        $post = $this->registry->retrieve($this->postId);
        $post->delete();
        $this->registry->removeByUrlKey('fixturepost');
        $this->registry->retrieveByUrlKey('fixturepost');
    }
}
