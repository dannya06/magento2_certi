<?php
namespace Aheadworks\Blog\Test\Integration\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Aheadworks\Blog\Model\CategoryRegistry
 */
class CategoryRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\CategoryRegistry
     */
    private $registry;

    /**
     * @var int|null
     */
    private $categoryId;

    protected function setUp()
    {
        $this->registry = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\CategoryRegistry');
        /** @var \Aheadworks\Blog\Model\Category $fixtureCategory */
        $fixtureCategory = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Category')
            ->load('fixturecategory', 'url_key');
        if ($fixtureCategory->getId()) {
            $this->categoryId = $fixtureCategory->getId();
        }
    }

    /**
     * Test of retrieving category instance
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testRetrieve()
    {
        $category = $this->registry->retrieve($this->categoryId);
        $this->assertInstanceOf('Aheadworks\Blog\Model\Category', $category);
        $this->assertEquals($this->categoryId, $category->getId());
    }

    /**
     * Test of retrieving category instance by url key
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testRetrieveByUrlKey()
    {
        $category = $this->registry->retrieveByUrlKey('fixturecategory');
        $this->assertInstanceOf('Aheadworks\Blog\Model\Category', $category);
        $this->assertEquals('fixturecategory', $category->getUrlKey());
    }

    /**
     * Test of caching of category instance
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testRetrieveCached()
    {
        $category = $this->registry->retrieve($this->categoryId);
        Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Category')
            ->load($this->categoryId)
            ->delete();
        $this->assertEquals($category, $this->registry->retrieve($this->categoryId));
        $this->assertEquals($category, $this->registry->retrieveByUrlKey('fixturecategory'));
    }

    /**
     * Testing that exception is thrown while retrieving of nonexistent category instance
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with categoryId = 333
     */
    public function testRetrieveException()
    {
        $categoryId = 333;
        $this->registry->retrieve($categoryId);
    }

    /**
     * Testing that exception is thrown while retrieving by url key of nonexistent category instance
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with urlKey = catnotexists
     */
    public function testRetrieveByUrlKeyException()
    {
        $categoryUrlKey = 'catnotexists';
        $this->registry->retrieveByUrlKey($categoryUrlKey);
    }

    /**
     * Test of removing category instance
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemove()
    {
        $category = $this->registry->retrieve($this->categoryId);
        $category->delete();
        $this->registry->remove($this->categoryId);
        $this->registry->retrieve($this->categoryId);
    }

    /**
     * Test of removing category instance by url key
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemoveByUrlKey()
    {
        $category = $this->registry->retrieve($this->categoryId);
        $category->delete();
        $this->registry->removeByUrlKey('fixturecategory');
        $this->registry->retrieveByUrlKey('fixturecategory');
    }
}
