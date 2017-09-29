<?php
namespace Aheadworks\Blog\Test\Integration\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Aheadworks\Blog\Model\ResourceModel\PostRepository
 */
class PostRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostInterfaceFactory
     */
    private $postFactory;

    /**
     * @var \Aheadworks\Blog\Model\PostRegistry
     */
    private $postRegistry;

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
    private $postId;

    protected function setUp()
    {
        $this->postRepository = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Api\PostRepositoryInterface');
        $this->postFactory = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Api\Data\PostInterfaceFactory');
        $this->postRegistry = Bootstrap::getObjectManager()
            ->get('Aheadworks\Blog\Model\PostRegistry');
        $this->dataObjectHelper = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Api\DataObjectHelper');
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Api\SearchCriteriaBuilder');
        /** @var \Aheadworks\Blog\Model\Post $fixturePost */
        $fixturePost = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Post')
            ->load('fixturepost', 'url_key');
        if ($fixturePost->getId()) {
            $this->postId = $fixturePost->getId();
        }
    }

    /**
     * Test of retrieve post
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testGet()
    {
        $post = $this->postRepository->get($this->postId);
        $this->assertInstanceOf('Aheadworks\Blog\Api\Data\PostInterface', $post);
        $this->assertEquals($this->postId, $post->getId());
    }

    /**
     * Testing that exception is thrown while retrieve of nonexistent post
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with postId = 444
     */
    public function testGetException()
    {
        $postId = 444;
        $this->postRepository->get($postId);
    }

    /**
     * Test of retrieve post by url key
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testGetByUrlKey()
    {
        $post = $this->postRepository->getByUrlKey('fixturepost');
        $this->assertInstanceOf('Aheadworks\Blog\Api\Data\PostInterface', $post);
        $this->assertEquals('fixturepost', $post->getUrlKey());
    }

    /**
     * Testing that exception is thrown while retrieve of nonexistent post by url key
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with urlKey = postnotexists
     */
    public function testGetByUrlKeyException()
    {
        $postUrlKey = 'postnotexists';
        $this->postRepository->getByUrlKey($postUrlKey);
    }

    /**
     * Test of creation new post
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     * @magentoDbIsolation enabled
     */
    public function testCreateNewPost()
    {
        $title = 'Post';
        $urlKey = 'post';
        $shortContent = 'Post short content';
        $content = 'Post content';
        $status = PostStatus::DRAFT;
        $authorId = Bootstrap::getObjectManager()->create('Magento\User\Model\User')
            ->load('dummy@dummy.com', 'email')
            ->getId();
        $authorName = 'Dummy Dummy';
        $isAllowComments = true;
        $storeIds = [1];
        $categoryIds = [
            Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Category')
                ->load('fixturecategory', 'url_key')
                ->getId()
        ];
        $tags = ['post tag 1', 'post tag 2'];
        $metaTitle = 'Post meta title';
        $metaDescription = 'Post meta description';

        /** @var \Aheadworks\Blog\Api\Data\PostInterface $newPostEntity */
        $newPostEntity = $this->postFactory->create();
        $newPostEntity
            ->setTitle($title)
            ->setUrlKey($urlKey)
            ->setShortContent($shortContent)
            ->setContent($content)
            ->setStatus($status)
            ->setAuthorId($authorId)
            ->setAuthorName($authorName)
            ->setIsAllowComments($isAllowComments)
            ->setStoreIds($storeIds)
            ->setCategoryIds($categoryIds)
            ->setTags($tags)
            ->setMetaTitle($metaTitle)
            ->setMetaDescription($metaDescription);
        $savedPostEntity = $this->postRepository->save($newPostEntity);
        $this->assertNotNull($savedPostEntity->getId());
        $this->assertEquals($title, $savedPostEntity->getTitle());
        $this->assertEquals($urlKey, $savedPostEntity->getUrlKey());
        $this->assertEquals($shortContent, $savedPostEntity->getShortContent());
        $this->assertEquals($content, $savedPostEntity->getContent());
        $this->assertEquals($status, $savedPostEntity->getStatus());
        $this->assertEquals($authorId, $savedPostEntity->getAuthorId());
        $this->assertEquals($authorName, $savedPostEntity->getAuthorName());
        $this->assertEquals($isAllowComments, $savedPostEntity->getIsAllowComments());
        $this->assertEquals($storeIds, $savedPostEntity->getStoreIds());
        $this->assertEquals($categoryIds, $savedPostEntity->getCategoryIds());
        $this->assertEquals($tags, $savedPostEntity->getTags());
        $this->assertEquals($metaTitle, $savedPostEntity->getMetaTitle());
        $this->assertEquals($metaDescription, $savedPostEntity->getMetaDescription());
    }

    /**
     * Test of update post
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testUpdatePost()
    {
        $titleUpdated = 'Post updated';
        $urlKeyUpdated = 'postupdated';
        $shortContentUpdated = 'Post short content updated';
        $contentUpdated = 'Post content updated';
        $isAllowCommentsUpdated = false;
        $tagsUpdated = ['posttag updated'];
        $metaTitleUpdated = 'Category meta title updated';
        $metaDescriptionUpdated = 'Category meta description updated';

        $postBefore = $this->postRepository->get($this->postId);
        $postData = array_merge(
            $postBefore->__toArray(),
            [
                PostInterface::TITLE => $titleUpdated,
                PostInterface::URL_KEY => $urlKeyUpdated,
                PostInterface::SHORT_CONTENT => $shortContentUpdated,
                PostInterface::CONTENT => $contentUpdated,
                PostInterface::IS_ALLOW_COMMENTS => $isAllowCommentsUpdated,
                PostInterface::TAGS => $tagsUpdated,
                PostInterface::META_TITLE => $metaTitleUpdated,
                PostInterface::META_DESCRIPTION => $metaDescriptionUpdated
            ]
        );
        /** @var \Aheadworks\Blog\Api\Data\PostInterface $post */
        $post = $this->postFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $post,
            $postData,
            'Aheadworks\Blog\Api\Data\PostInterface'
        );
        $postAfter = $this->postRepository->save($post);
        $this->assertEquals($titleUpdated, $postAfter->getTitle());
        $this->assertEquals($urlKeyUpdated, $postAfter->getUrlKey());
        $this->assertEquals($shortContentUpdated, $postAfter->getShortContent());
        $this->assertEquals($contentUpdated, $postAfter->getContent());
        $this->assertEquals($isAllowCommentsUpdated, $postAfter->getIsAllowComments());
        $this->assertEquals($tagsUpdated, $postAfter->getTags());
        $this->assertEquals($metaTitleUpdated, $postAfter->getMetaTitle());
        $this->assertEquals($metaDescriptionUpdated, $postAfter->getMetaDescription());
    }

    /**
     * Testing that exception is thrown while update of post with incorrect data
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testUpdatePostException()
    {
        $urlKeyIncorrect = '123';
        $post = $this->postRepository->get($this->postId);
        $post->setUrlKey($urlKeyIncorrect);
        $this->postRepository->save($post);
    }

    /**
     * Test of delete post
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testDelete()
    {
        $post = $this->postRepository->get($this->postId);
        $this->postRepository->delete($post);
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with postId = ' . $this->postId
        );
        $this->postRepository->get($this->postId);
    }

    /**
     * Testing that exception is thrown while delete of nonexistent post
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testDeleteException()
    {
        $post = $this->postRepository->get($this->postId);
        Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Post')
            ->load($this->postId)
            ->delete();
        $this->postRegistry->remove($this->postId);
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with postId = ' . $this->postId
        );
        $this->postRepository->delete($post);
    }

    /**
     * Test of delete post by Id
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testDeleteById()
    {
        $this->postRepository->deleteById($this->postId);
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            'No such entity with postId = ' . $this->postId
        );
        $this->postRepository->get($this->postId);
    }

    /**
     * Testing that exception is thrown while delete by Id of nonexistent post
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with postId = 444
     */
    public function testDeleteByIdException()
    {
        $postId = 444;
        $this->postRepository->deleteById($postId);
    }

    /**
     * Test of retrieve list posts
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts_with_different_statuses.php
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

        $searchResults = $this->postRepository->getList($this->searchCriteriaBuilder->create());
        $this->assertEquals(count($expectedResult), $searchResults->getTotalCount());
        foreach ($searchResults->getItems() as $item) {
            $this->assertEquals($expectedResult[$item->getUrlKey()][PostInterface::TITLE], $item->getTitle());
            $this->assertEquals($expectedResult[$item->getUrlKey()][PostInterface::VIRTUAL_STATUS], $item->getVirtualStatus());
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
                [$filterBuilder->setField(PostInterface::TITLE)->setValue('Post')->create()],
                null,
                [
                    'draftedpost' => [
                        PostInterface::TITLE => 'Post',
                        PostInterface::VIRTUAL_STATUS => PostStatus::DRAFT
                    ]
                ]
            ],
            'and' => [
                [
                    $filterBuilder->setField(PostInterface::TITLE)->setValue('Post')->create(),
                    $filterBuilder->setField(PostInterface::CONTENT)->setValue('Content')->create()
                ],
                null,
                [
                    'draftedpost' => [
                        PostInterface::TITLE => 'Post',
                        PostInterface::VIRTUAL_STATUS => PostStatus::DRAFT
                    ]
                ]
            ],
            'or' => [
                [],
                [
                    $filterBuilder->setField(PostInterface::TITLE)->setValue('Post')->create(),
                    $filterBuilder->setField(PostInterface::TITLE)->setValue('Post published')->create()
                ],
                [
                    'draftedpost' => [
                        PostInterface::TITLE => 'Post',
                        PostInterface::VIRTUAL_STATUS => PostStatus::DRAFT
                    ],
                    'publishedpost' => [
                        PostInterface::TITLE => 'Post published',
                        PostInterface::VIRTUAL_STATUS => PostStatus::PUBLICATION_PUBLISHED
                    ]
                ]
            ],
            'like' => [
                [$filterBuilder->setField(PostInterface::TITLE)->setValue('%ed%')->setConditionType('like')->create()],
                null,
                [
                    'publishedpost' => [
                        PostInterface::TITLE => 'Post published',
                        PostInterface::VIRTUAL_STATUS => PostStatus::PUBLICATION_PUBLISHED
                    ],
                    'scheduledpost' => [
                        PostInterface::TITLE => 'Post scheduled',
                        PostInterface::VIRTUAL_STATUS => PostStatus::PUBLICATION_SCHEDULED
                    ]
                ]
            ]
        ];
    }
}
