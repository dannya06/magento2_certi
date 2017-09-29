<?php
namespace Aheadworks\Blog\Test\Integration\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Aheadworks\Blog\Model\TagRegistry
 */
class TagRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\TagRegistry
     */
    private $registry;

    /**
     * @var int|null
     */
    private $tagId;

    protected function setUp()
    {
        $this->registry = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\TagRegistry');
        /** @var \Aheadworks\Blog\Model\Tag $fixtureTag */
        $fixtureTag = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Tag')
            ->load('fixturetag', 'name');
        if ($fixtureTag->getId()) {
            $this->tagId = $fixtureTag->getId();
        }
    }

    /**
     * Test of retrieving tag instance
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     */
    public function testRetrieve()
    {
        $tag = $this->registry->retrieve($this->tagId);
        $this->assertInstanceOf('Aheadworks\Blog\Model\Tag', $tag);
        $this->assertEquals($this->tagId, $tag->getId());
    }

    /**
     * Test of retrieving tag instance by name
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     */
    public function testRetrieveByName()
    {
        $tag = $this->registry->retrieveByName('fixturetag');
        $this->assertInstanceOf('Aheadworks\Blog\Model\Tag', $tag);
        $this->assertEquals('fixturetag', $tag->getName());
    }

    /**
     * Test of caching of tag instance
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     */
    public function testRetrieveCached()
    {
        $tag = $this->registry->retrieve($this->tagId);
        Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Tag')
            ->load($this->tagId)
            ->delete();
        $this->assertEquals($tag, $this->registry->retrieve($this->tagId));
        $this->assertEquals($tag, $this->registry->retrieveByName('fixturetag'));
    }

    /**
     * Testing that exception is thrown while retrieving of nonexistent tag instance
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with tagId = 333
     */
    public function testRetrieveException()
    {
        $tagId = 333;
        $this->registry->retrieve($tagId);
    }

    /**
     * Testing that exception is thrown while retrieving by name of nonexistent tag instance
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with name = tagnotexists
     */
    public function testRetrieveByNameException()
    {
        $tagName = 'tagnotexists';
        $this->registry->retrieveByName($tagName);
    }

    /**
     * Test of removing tag instance
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemove()
    {
        $tag = $this->registry->retrieve($this->tagId);
        $tag->delete();
        $this->registry->remove($this->tagId);
        $this->registry->retrieve($this->tagId);
    }

    /**
     * Test of removing tag instance by name
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemoveByName()
    {
        $tag = $this->registry->retrieve($this->tagId);
        $tag->delete();
        $this->registry->removeByName('fixturetag');
        $this->registry->retrieveByName('fixturetag');
    }
}
