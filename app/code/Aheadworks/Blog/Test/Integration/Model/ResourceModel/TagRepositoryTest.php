<?php
namespace Aheadworks\Blog\Test\Integration\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\TagInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Aheadworks\Blog\Model\ResourceModel\TagRepository
 */
class TagRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Api\TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagInterfaceFactory
     */
    private $tagFactory;

    /**
     * @var \Aheadworks\Blog\Model\TagRegistry
     */
    private $tagRegistry;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var int|null
     */
    private $tagId;

    protected function setUp()
    {
        $this->tagRepository = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Api\TagRepositoryInterface');
        $this->tagFactory = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Api\Data\TagInterfaceFactory');
        $this->tagRegistry = Bootstrap::getObjectManager()
            ->get('Aheadworks\Blog\Model\TagRegistry');
        $this->dataObjectHelper = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Api\DataObjectHelper');
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Api\SearchCriteriaBuilder');
        /** @var \Aheadworks\Blog\Model\Tag $fixtureTag */
        $fixtureTag = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Tag')
            ->load('fixturetag', 'name');
        if ($fixtureTag->getId()) {
            $this->tagId = $fixtureTag->getId();
        }
    }

    /**
     * Test of retrieve tag
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     */
    public function testGet()
    {
        $tag = $this->tagRepository->get($this->tagId);
        $this->assertInstanceOf('Aheadworks\Blog\Api\Data\TagInterface', $tag);
        $this->assertEquals($this->tagId, $tag->getId());
    }

    /**
     * Testing that exception is thrown while retrieve of nonexistent tag
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with tagId = 333
     */
    public function testGetException()
    {
        $tagId = 333;
        $this->tagRepository->get($tagId);
    }

    /**
     * Test of retrieve tag by name
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     */
    public function testGetByName()
    {
        $tag = $this->tagRepository->getByName('fixturetag');
        $this->assertInstanceOf('Aheadworks\Blog\Api\Data\TagInterface', $tag);
        $this->assertEquals('fixturetag', $tag->getName());
    }

    /**
     * Testing that exception is thrown while retrieve of nonexistent tag by name
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with name = tagnotexists
     */
    public function testGetByNameException()
    {
        $tagName = 'tagnotexists';
        $this->tagRepository->getByName($tagName);
    }

    /**
     * Test of creation new tag
     *
     * @magentoDbIsolation enabled
     */
    public function testCreateNewTag()
    {
        $name = 'New tag';
        /** @var \Aheadworks\Blog\Api\Data\TagInterface $newTagEntity */
        $newTagEntity = $this->tagFactory->create();
        $newTagEntity->setName($name);
        $savedTagEntity = $this->tagRepository->save($newTagEntity);
        $this->assertNotNull($savedTagEntity->getId());
        $this->assertEquals($name, $savedTagEntity->getName());
    }

    /**
     * Test of update tag
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     */
    public function testUpdateTag()
    {
        $nameUpdated = 'fixturetag updated';
        $tagBefore = $this->tagRepository->get($this->tagId);
        $tagData = array_merge($tagBefore->__toArray(), [TagInterface::NAME => $nameUpdated]);
        /** @var \Aheadworks\Blog\Api\Data\TagInterface $tag */
        $tag = $this->tagFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $tag,
            $tagData,
            'Aheadworks\Blog\Api\Data\TagInterface'
        );
        $tagAfter = $this->tagRepository->save($tag);
        $this->assertEquals($nameUpdated, $tagAfter->getName());
    }

    /**
     * Testing that exception is thrown while update of tag with incorrect data
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testUpdateTagException()
    {
        $nameIncorrect = '';
        $tag = $this->tagRepository->get($this->tagId);
        $tag->setName($nameIncorrect);
        $this->tagRepository->save($tag);
    }

    /**
     * Test of delete tag
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     */
    public function testDelete()
    {
        $tag = $this->tagRepository->get($this->tagId);
        $this->tagRepository->delete($tag);
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with tagId = ' . $this->tagId
        );
        $this->tagRepository->get($this->tagId);
    }

    /**
     * Testing that exception is thrown while delete of nonexistent tag
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     */
    public function testDeleteException()
    {
        $tag = $this->tagRepository->get($this->tagId);
        Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Tag')
            ->load($this->tagId)
            ->delete();
        $this->tagRegistry->remove($this->tagId);
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with tagId = ' . $this->tagId
        );
        $this->tagRepository->delete($tag);
    }

    /**
     * Test of delete tag by Id
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tag.php
     */
    public function testDeleteById()
    {
        $this->tagRepository->deleteById($this->tagId);
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with tagId = ' . $this->tagId
        );
        $this->tagRepository->get($this->tagId);
    }

    /**
     * Testing that exception is thrown while delete by Id of nonexistent tag
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with tagId = 333
     */
    public function testDeleteByIdException()
    {
        $tagId = 333;
        $this->tagRepository->deleteById($tagId);
    }

    /**
     * Test of retrieve list tags
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/three_tags.php
     * @dataProvider getListDataProvider
     */
    public function testGetList($filters, $filterGroup, $expectedResult)
    {
        foreach ($filters as $filter) {
            $this->searchCriteriaBuilder->addFilters([$filter]);
        }
        if ($filterGroup !== null) {
            $this->searchCriteriaBuilder->addFilters($filterGroup);
        }

        $searchResults = $this->tagRepository->getList($this->searchCriteriaBuilder->create());
        $this->assertEquals(count($expectedResult), $searchResults->getTotalCount());
        foreach ($searchResults->getItems() as $item) {
            $this->assertEquals($expectedResult[$item->getName()][TagInterface::NAME], $item->getName());
        }
    }

    /**
     * @return array
     */
    public function getListDataProvider()
    {
        /** @var \Magento\Framework\Api\FilterBuilder $filterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create('Magento\Framework\Api\FilterBuilder');
        return [
            'eq' => [
                [$filterBuilder->setField(TagInterface::NAME)->setValue('tag 1')->create()],
                null,
                [
                    'tag 1' => [TagInterface::NAME => 'tag 1']
                ]
            ],
            'like' => [
                [$filterBuilder->setField(TagInterface::NAME)->setValue('%tag%')->setConditionType('like')->create()],
                null,
                [
                    'tag 1' => [TagInterface::NAME => 'tag 1'],
                    'second tag' => [TagInterface::NAME => 'second tag']
                ]
            ],
            'and' => [
                [
                    $filterBuilder->setField(TagInterface::NAME)->setValue('tag 1')->create(),
                    $filterBuilder->setField(TagInterface::NAME)->setValue('%tag%')->setConditionType('like')->create()
                ],
                null,
                [
                    'tag 1' => [TagInterface::NAME => 'tag 1']
                ]
            ],
            'or' => [
                [],
                [
                    $filterBuilder->setField(TagInterface::NAME)->setValue('tag 1')->create(),
                    $filterBuilder->setField(TagInterface::NAME)->setValue('third one')->create(),
                ],
                [
                    'tag 1' => [TagInterface::NAME => 'tag 1'],
                    'third one' => [TagInterface::NAME => 'third one']
                ]
            ]
        ];
    }
}
