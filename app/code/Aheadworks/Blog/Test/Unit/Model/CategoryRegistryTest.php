<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\CategoryRegistry
 */
class CategoryRegistryTest extends \PHPUnit_Framework_TestCase
{
    const CATEGORY_ID = 1;
    const URL_KEY = 'cat';

    /**
     * @var \Aheadworks\Blog\Model\CategoryRegistry
     */
    private $categoryRegistry;

    /**
     * @var \Aheadworks\Blog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->category = $this->getMockBuilder('Aheadworks\Blog\Model\Category')
            ->setMethods(['load', 'loadByUrlKey', 'getId', 'getUrlKey'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryFactoryStab = $this->getMockBuilder('Aheadworks\Blog\Model\CategoryFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryFactoryStab->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->category));
        $this->categoryRegistry = $objectManager->getObject(
            'Aheadworks\Blog\Model\CategoryRegistry',
            ['categoryFactory' => $categoryFactoryStab]
        );
    }

    /**
     * Testing that the Category Model is load during retrieving
     */
    public function testRetrieveLoadModel()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->category->expects($this->once())
            ->method('load')
            ->with(self::CATEGORY_ID)
            ->will($this->returnValue($this->category));
        $this->categoryRegistry->retrieve(self::CATEGORY_ID);
    }

    /**
     * Testing that the Category Model is cached during retrieving
     */
    public function testRetrieveCaching()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->categoryRegistry->retrieve(self::CATEGORY_ID);
        $this->category->expects($this->never())
            ->method('load')
            ->with(self::CATEGORY_ID)
            ->will($this->returnValue($this->category));
        $this->categoryRegistry->retrieve(self::CATEGORY_ID);
    }

    /**
     * Testing retrieve result
     */
    public function testRetrieveResult()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->category->expects($this->any())
            ->method('load')
            ->with(self::CATEGORY_ID)
            ->will($this->returnValue($this->category));
        $this->assertEquals(
            $this->category,
            $this->categoryRegistry->retrieve(self::CATEGORY_ID)
        );
    }

    /**
     * Testing exception while retrieving of non-existent Category Model
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRetrieveException()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(null));
        $this->category->expects($this->any())
            ->method('load')
            ->with(self::CATEGORY_ID)
            ->will($this->returnValue($this->category));
        $this->categoryRegistry->retrieve(self::CATEGORY_ID);
    }

    /**
     * Testing that the Category Model is load during retrieving by url-key
     */
    public function testRetrieveByUrlKeyLoadModel()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->category->expects($this->once())
            ->method('loadByUrlKey')
            ->with(self::URL_KEY)
            ->will($this->returnValue($this->category));
        $this->categoryRegistry->retrieveByUrlKey(self::URL_KEY);
    }

    /**
     * Testing that the Category Model is cached during retrieving by url-key
     */
    public function testRetrieveByUrlKeyCaching()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->categoryRegistry->retrieveByUrlKey(self::URL_KEY);
        $this->category->expects($this->never())
            ->method('loadByUrlKey')
            ->with(self::URL_KEY)
            ->will($this->returnValue($this->category));
        $this->categoryRegistry->retrieveByUrlKey(self::URL_KEY);
    }

    /**
     * Testing retrieve by url-key result
     */
    public function testRetrieveByUrlKeyResult()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->category->expects($this->any())
            ->method('loadByUrlKey')
            ->with(self::URL_KEY)
            ->will($this->returnValue($this->category));
        $this->assertEquals(
            $this->category,
            $this->categoryRegistry->retrieveByUrlKey(self::URL_KEY)
        );
    }

    /**
     * Testing exception while retrieving by url-key of non-existent Category Model
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRetrieveByUrlKeyException()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(null));
        $this->category->expects($this->any())
            ->method('loadByUrlKey')
            ->with(self::URL_KEY)
            ->will($this->returnValue($this->category));
        $this->categoryRegistry->retrieveByUrlKey(self::URL_KEY);
    }

    /**
     * Testing remove
     */
    public function testRemove()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->exactly(2))
            ->method('load')
            ->with(self::CATEGORY_ID)
            ->will($this->returnValue($this->category));
        $this->categoryRegistry->retrieve(self::CATEGORY_ID);
        $this->categoryRegistry->remove(self::CATEGORY_ID);
        $this->categoryRegistry->retrieve(self::CATEGORY_ID);
    }

    /**
     * Testing remove by url-key
     */
    public function testRemoveByUrlKey()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->category->expects($this->exactly(2))
            ->method('load')
            ->with(self::CATEGORY_ID)
            ->will($this->returnValue($this->category));
        $this->categoryRegistry->retrieve(self::CATEGORY_ID);
        $this->categoryRegistry->removeByUrlKey(self::URL_KEY);
        $this->categoryRegistry->retrieve(self::CATEGORY_ID);
    }

    /**
     * Test push
     */
    public function testPush()
    {
        $this->category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $this->category->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->categoryRegistry->retrieve(self::CATEGORY_ID);
        /** @var \Aheadworks\Blog\Model\Category|\PHPUnit_Framework_MockObject_MockObject $newCategory */
        $newCategory = $this->getMockBuilder('Aheadworks\Blog\Model\Category')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getUrlKey'])
            ->getMock();
        $newCategory->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::CATEGORY_ID));
        $newCategory->expects($this->any())
            ->method('getUrlKey')
            ->will($this->returnValue(self::URL_KEY));
        $this->categoryRegistry->push($newCategory);
        $this->assertEquals($newCategory, $this->categoryRegistry->retrieve(self::CATEGORY_ID));
    }
}
