<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\TagRegistry
 */
class TagRegistryTest extends \PHPUnit_Framework_TestCase
{
    const TAG_ID = 1;
    const NAME = 'Tag';

    /**
     * @var \Aheadworks\Blog\Model\TagRegistry
     */
    private $tagRegistry;

    /**
     * @var \Aheadworks\Blog\Model\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tag;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->tag = $this->getMockBuilder('Aheadworks\Blog\Model\tag')
            ->setMethods(['load', 'loadByName', 'getId', 'getName'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagFactoryStab = $this->getMockBuilder('Aheadworks\Blog\Model\TagFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagFactoryStab->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->tag));
        $this->tagRegistry = $objectManager->getObject(
            'Aheadworks\Blog\Model\TagRegistry',
            ['tagFactory' => $tagFactoryStab]
        );
    }

    /**
     * Testing that the Tag Model is load during retrieving
     */
    public function testRetrieveLoadModel()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::NAME));
        $this->tag->expects($this->once())
            ->method('load')
            ->with(self::TAG_ID)
            ->will($this->returnValue($this->tag));
        $this->tagRegistry->retrieve(self::TAG_ID);
    }

    /**
     * Testing that the Tag Model is cached during retrieving
     */
    public function testRetrieveCaching()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::NAME));
        $this->tagRegistry->retrieve(self::TAG_ID);
        $this->tag->expects($this->never())
            ->method('load')
            ->with(self::TAG_ID)
            ->will($this->returnValue($this->tag));
        $this->tagRegistry->retrieve(self::TAG_ID);
    }

    /**
     * Testing retrieve result
     */
    public function testRetrieveResult()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::NAME));
        $this->tag->expects($this->any())
            ->method('load')
            ->with(self::TAG_ID)
            ->will($this->returnValue($this->tag));
        $this->assertEquals(
            $this->tag,
            $this->tagRegistry->retrieve(self::TAG_ID)
        );
    }

    /**
     * Testing exception while retrieving of non-existent Tag Model
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRetrieveException()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(null));
        $this->tag->expects($this->any())
            ->method('load')
            ->with(self::TAG_ID)
            ->will($this->returnValue($this->tag));
        $this->tagRegistry->retrieve(self::TAG_ID);
    }

    /**
     * Testing that the Tag Model is load during retrieving by name
     */
    public function testRetrieveByNameLoadModel()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::NAME));
        $this->tag->expects($this->once())
            ->method('loadByName')
            ->with(self::NAME)
            ->will($this->returnValue($this->tag));
        $this->tagRegistry->retrieveByName(self::NAME);
    }

    /**
     * Testing that the Tag Model is cached during retrieving by name
     */
    public function testRetrieveByNameCaching()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::NAME));
        $this->tagRegistry->retrieveByName(self::NAME);
        $this->tag->expects($this->never())
            ->method('loadByName')
            ->with(self::NAME)
            ->will($this->returnValue($this->tag));
        $this->tagRegistry->retrieveByName(self::NAME);
    }

    /**
     * Testing retrieve by name result
     */
    public function testRetrieveByNameResult()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::NAME));
        $this->tag->expects($this->any())
            ->method('loadByName')
            ->with(self::NAME)
            ->will($this->returnValue($this->tag));
        $this->assertEquals(
            $this->tag,
            $this->tagRegistry->retrieveByName(self::NAME)
        );
    }

    /**
     * Testing exception while retrieving by name of non-existent Tag Model
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRetrieveByNameException()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(null));
        $this->tag->expects($this->any())
            ->method('loadByName')
            ->with(self::NAME)
            ->will($this->returnValue($this->tag));
        $this->tagRegistry->retrieveByName(self::NAME);
    }

    /**
     * Testing remove
     */
    public function testRemove()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $this->tag->expects($this->exactly(2))
            ->method('load')
            ->with(self::TAG_ID)
            ->will($this->returnValue($this->tag));
        $this->tagRegistry->retrieve(self::TAG_ID);
        $this->tagRegistry->remove(self::TAG_ID);
        $this->tagRegistry->retrieve(self::TAG_ID);
    }

    /**
     * Testing remove by name
     */
    public function testRemoveByName()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::NAME));
        $this->tag->expects($this->exactly(2))
            ->method('load')
            ->with(self::TAG_ID)
            ->will($this->returnValue($this->tag));
        $this->tagRegistry->retrieve(self::TAG_ID);
        $this->tagRegistry->removeByName(self::NAME);
        $this->tagRegistry->retrieve(self::TAG_ID);
    }

    /**
     * Test push
     */
    public function testPush()
    {
        $this->tag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::NAME));
        $this->tagRegistry->retrieve(self::TAG_ID);
        /** @var \Aheadworks\Blog\Model\Tag|\PHPUnit_Framework_MockObject_MockObject $newTag */
        $newTag = $this->getMockBuilder('Aheadworks\Blog\Model\Tag')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getName'])
            ->getMock();
        $newTag->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAG_ID));
        $newTag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::NAME));
        $this->tagRegistry->push($newTag);
        $this->assertEquals($newTag, $this->tagRegistry->retrieve(self::TAG_ID));
    }
}
