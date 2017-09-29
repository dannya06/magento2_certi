<?php
namespace Aheadworks\Blog\Test\Integration\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Aheadworks\Blog\Model\ResourceModel\CategoryRepository
 */
class CategoryRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var \Aheadworks\Blog\Model\CategoryRegistry
     */
    private $categoryRegistry;

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
    private $categoryId;

    protected function setUp()
    {
        $this->categoryRepository = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $this->categoryFactory = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Api\Data\CategoryInterfaceFactory');
        $this->categoryRegistry = Bootstrap::getObjectManager()
            ->get('Aheadworks\Blog\Model\CategoryRegistry');
        $this->dataObjectHelper = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Api\DataObjectHelper');
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Api\SearchCriteriaBuilder');
        /** @var \Aheadworks\Blog\Model\Category $fixtureCategory */
        $fixtureCategory = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Category')
            ->load('fixturecategory', 'url_key');
        if ($fixtureCategory->getId()) {
            $this->categoryId = $fixtureCategory->getId();
        }
    }

    /**
     * Test of retrieve category
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testGet()
    {
        $category = $this->categoryRepository->get($this->categoryId);
        $this->assertInstanceOf('Aheadworks\Blog\Api\Data\CategoryInterface', $category);
        $this->assertEquals($this->categoryId, $category->getId());
    }

    /**
     * Testing that exception is thrown while retrieve of nonexistent category
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with categoryId = 555
     */
    public function testGetException()
    {
        $categoryId = 555;
        $this->categoryRepository->get($categoryId);
    }

    /**
     * Test of retrieve category by url key
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testGetByUrlKey()
    {
        $category = $this->categoryRepository->getByUrlKey('fixturecategory');
        $this->assertInstanceOf('Aheadworks\Blog\Api\Data\CategoryInterface', $category);
        $this->assertEquals('fixturecategory', $category->getUrlKey());
    }

    /**
     * Testing that exception is thrown while retrieve of nonexistent category by url key
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with urlKey = catnotexists
     */
    public function testGetByUrlKeyException()
    {
        $categoryUrlKey = 'catnotexists';
        $this->categoryRepository->getByUrlKey($categoryUrlKey);
    }

    /**
     * Test of creation new category
     *
     * @magentoDbIsolation enabled
     */
    public function testCreateNewCategory()
    {
        $name = 'Category';
        $urlKey = 'category';
        $status = \Aheadworks\Blog\Model\Category::STATUS_ENABLED;
        $sortOrder = 0;
        $storeIds = [1];
        $metaTitle = 'Category meta title';
        $metaDescription = 'Category meta description';

        /** @var \Aheadworks\Blog\Api\Data\CategoryInterface $newCategoryEntity */
        $newCategoryEntity = $this->categoryFactory->create();
        $newCategoryEntity
            ->setName($name)
            ->setUrlKey($urlKey)
            ->setStatus($status)
            ->setSortOrder($sortOrder)
            ->setStoreIds($storeIds)
            ->setMetaTitle($metaTitle)
            ->setMetaDescription($metaDescription);

        $savedCategoryEntity = $this->categoryRepository->save($newCategoryEntity);
        $this->assertNotNull($savedCategoryEntity->getId());
        $this->assertEquals($name, $savedCategoryEntity->getName());
        $this->assertEquals($urlKey, $savedCategoryEntity->getUrlKey());
        $this->assertEquals($status, $savedCategoryEntity->getStatus());
        $this->assertEquals($sortOrder, $savedCategoryEntity->getSortOrder());
        $this->assertEquals($storeIds, $savedCategoryEntity->getStoreIds());
        $this->assertEquals($metaTitle, $savedCategoryEntity->getMetaTitle());
        $this->assertEquals($metaDescription, $savedCategoryEntity->getMetaDescription());
    }

    /**
     * Test of update category
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testUpdateCategory()
    {
        $nameUpdated = 'Category updated';
        $urlKeyUpdated = 'categoryupdated';
        $sortOrderUpdated = 1;
        $metaTitleUpdated = 'Category meta title updated';
        $metaDescriptionUpdated = 'Category meta description updated';

        $categoryBefore = $this->categoryRepository->get($this->categoryId);
        $categoryData = array_merge(
            $categoryBefore->__toArray(),
            [
                CategoryInterface::NAME => $nameUpdated,
                CategoryInterface::URL_KEY => $urlKeyUpdated,
                CategoryInterface::SORT_ORDER => $sortOrderUpdated,
                CategoryInterface::META_TITLE => $metaTitleUpdated,
                CategoryInterface::META_DESCRIPTION => $metaDescriptionUpdated
            ]
        );
        /** @var \Aheadworks\Blog\Api\Data\CategoryInterface $category */
        $category = $this->categoryFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $category,
            $categoryData,
            'Aheadworks\Blog\Api\Data\CategoryInterface'
        );
        $categoryAfter = $this->categoryRepository->save($category);
        $this->assertEquals($nameUpdated, $categoryAfter->getName());
        $this->assertEquals($urlKeyUpdated, $categoryAfter->getUrlKey());
        $this->assertEquals($sortOrderUpdated, $categoryAfter->getSortOrder());
        $this->assertEquals($metaTitleUpdated, $categoryAfter->getMetaTitle());
        $this->assertEquals($metaDescriptionUpdated, $categoryAfter->getMetaDescription());
    }

    /**
     * Testing that exception is thrown while update of category with incorrect data
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testUpdateCategoryException()
    {
        $urlKeyIncorrect = '123';
        $category = $this->categoryRepository->get($this->categoryId);
        $category->setUrlKey($urlKeyIncorrect);
        $this->categoryRepository->save($category);
    }

    /**
     * Test of delete category
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testDelete()
    {
        $category = $this->categoryRepository->get($this->categoryId);
        $this->categoryRepository->delete($category);
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with categoryId = ' . $this->categoryId
        );
        $this->categoryRepository->get($this->categoryId);
    }

    /**
     * Testing that exception is thrown while delete of nonexistent category
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testDeleteException()
    {
        $category = $this->categoryRepository->get($this->categoryId);
        Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Category')
            ->load($this->categoryId)
            ->delete();
        $this->categoryRegistry->remove($this->categoryId);
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with categoryId = ' . $this->categoryId
        );
        $this->categoryRepository->delete($category);
    }

    /**
     * Test of delete category by Id
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testDeleteById()
    {
        $this->categoryRepository->deleteById($this->categoryId);
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with categoryId = ' . $this->categoryId
        );
        $this->categoryRepository->get($this->categoryId);
    }

    /**
     * Testing that exception is thrown while delete by Id of nonexistent category
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with categoryId = 555
     */
    public function testDeleteByIdException()
    {
        $categoryId = 555;
        $this->categoryRepository->deleteById($categoryId);
    }

    /**
     * Test of retrieve list categories
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/three_categories.php
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

        $searchResults = $this->categoryRepository->getList($this->searchCriteriaBuilder->create());
        $this->assertEquals(count($expectedResult), $searchResults->getTotalCount());
        foreach ($searchResults->getItems() as $item) {
            $this->assertEquals($expectedResult[$item->getUrlKey()][CategoryInterface::NAME], $item->getName());
            $this->assertEquals($expectedResult[$item->getUrlKey()][CategoryInterface::STATUS], $item->getStatus());
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
                [$filterBuilder->setField(CategoryInterface::NAME)->setValue('Category 1')->create()],
                null,
                [
                    'fixturecategory1' => [
                        CategoryInterface::NAME => 'Category 1',
                        CategoryInterface::STATUS => Category::STATUS_ENABLED
                    ]
                ]
            ],
            'and' => [
                [
                    $filterBuilder->setField(CategoryInterface::NAME)->setValue('Category 1')->create(),
                    $filterBuilder->setField(CategoryInterface::STATUS)->setValue(Category::STATUS_ENABLED)->create()
                ],
                null,
                [
                    'fixturecategory1' => [
                        CategoryInterface::NAME => 'Category 1',
                        CategoryInterface::STATUS => Category::STATUS_ENABLED
                    ]
                ]
            ],
            'or' => [
                [],
                [
                    $filterBuilder->setField(CategoryInterface::NAME)->setValue('Category 1')->create(),
                    $filterBuilder->setField(CategoryInterface::NAME)->setValue('Cat 3')->create()
                ],
                [
                    'fixturecategory1' => [
                        CategoryInterface::NAME => 'Category 1',
                        CategoryInterface::STATUS => Category::STATUS_ENABLED
                    ],
                    'fixturecategory3' => [
                        CategoryInterface::NAME => 'Cat 3',
                        CategoryInterface::STATUS => Category::STATUS_DISABLED
                    ]
                ]
            ],
            'like' => [
                [$filterBuilder->setField(CategoryInterface::NAME)->setValue('%ory%')->setConditionType('like')->create()],
                null,
                [
                    'fixturecategory1' => [
                        CategoryInterface::NAME => 'Category 1',
                        CategoryInterface::STATUS => Category::STATUS_ENABLED
                    ],
                    'fixturecategory2' => [
                        CategoryInterface::NAME => 'Second category',
                        CategoryInterface::STATUS => Category::STATUS_ENABLED
                    ]
                ]
            ]
        ];
    }
}
