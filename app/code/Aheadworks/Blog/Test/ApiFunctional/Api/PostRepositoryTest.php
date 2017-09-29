<?php
namespace Aheadworks\Blog\Test\ApiFunctional\Api;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Tests for blog post service.
 */
class PostRepositoryTest extends WebapiAbstract
{
    const SERVICE_NAME = 'aheadworksBlogPostRepositoryV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/blogPost';

    /**
     * @var \Aheadworks\Blog\Api\Data\PostInterfaceFactory
     */
    private $postFactory;

    /**
     * @var \Aheadworks\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Aheadworks\Blog\Model\PostRegistry
     */
    private $postRegistry;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PostInterface|null
     */
    private $post;

    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->postFactory = $objectManager->create('Aheadworks\Blog\Api\Data\PostInterfaceFactory');
        $this->postRepository = $objectManager->create('Aheadworks\Blog\Api\PostRepositoryInterface');
        $this->postRegistry = $objectManager->get('Aheadworks\Blog\Model\PostRegistry');
        $this->dataObjectHelper = $objectManager->create('Magento\Framework\Api\DataObjectHelper');
        $this->dataObjectProcessor = $objectManager->create('Magento\Framework\Reflection\DataObjectProcessor');
        $this->filterBuilder = $objectManager->create('Magento\Framework\Api\FilterBuilder');
        $this->searchCriteriaBuilder = $objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
    }

    public function tearDown()
    {
        if ($this->post) {
            $this->postRepository->delete($this->post);
            $this->post = null;
        }
    }

    /**
     * Test get \Aheadworks\Blog\Api\Data\PostInterface
     */
    public function testGet()
    {
        $postDataObject = $this->createPostDataObject();
        $this->post = $this->postRepository->save($postDataObject);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $this->post->getId(),
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];

        $post = $this->_webApiCall($serviceInfo, ['postId' => $this->post->getId()]);
        $this->assertNotNull($post['id']);
        $this->assertEquals($this->post->getUrlKey(), $post[PostInterface::URL_KEY]);
    }

    /**
     * Test create \Aheadworks\Blog\Api\Data\PostInterface
     */
    public function testCreate()
    {
        $postDataObject = $this->createPostDataObject();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];

        $requestData = ['post' => [
            PostInterface::TITLE => $postDataObject->getTitle(),
            PostInterface::URL_KEY => $postDataObject->getUrlKey(),
            PostInterface::CONTENT => $postDataObject->getContent(),
            PostInterface::STATUS => $postDataObject->getStatus(),
            PostInterface::IS_ALLOW_COMMENTS => $postDataObject->getIsAllowComments(),
            PostInterface::STORE_IDS => $postDataObject->getStoreIds(),
        ]];
        $post = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($post['id']);

        $this->post = $this->postRepository->get($post['id']);
        $this->assertEquals($this->post->getUrlKey(), $postDataObject->getUrlKey());
    }

    /**
     * Test update \Aheadworks\Blog\Api\Data\PostInterface using POST
     */
    public function testUpdateUsingPOST()
    {
        $this->post = $this->postRepository->save($this->createPostDataObject());
        $this->updateTest(
            $this->post,
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH,
                    'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
                ],
                'soap' => [
                    'service' => self::SERVICE_NAME,
                    'serviceVersion' => self::SERVICE_VERSION,
                    'operation' => self::SERVICE_NAME . 'Save',
                ],
            ]
        );
    }

    /**
     * Test update \Aheadworks\Blog\Api\Data\PostInterface using PUT
     */
    public function testUpdateUsingPUT()
    {
        $this->post = $this->postRepository->save($this->createPostDataObject());
        $this->updateTest(
            $this->post,
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $this->post->getId(),
                    'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_PUT,
                ],
                'soap' => [
                    'service' => self::SERVICE_NAME,
                    'serviceVersion' => self::SERVICE_VERSION,
                    'operation' => self::SERVICE_NAME . 'Save',
                ],
            ]
        );
    }

    /**
     * Test update \Aheadworks\Blog\Api\Data\PostInterface
     *
     * @param PostInterface $postDataObject
     * @param array $serviceInfo
     */
    private function updateTest($postDataObject, $serviceInfo)
    {
        $newTitle = $postDataObject->getTitle() . '-new';

        $this->dataObjectHelper->populateWithArray(
            $postDataObject,
            [PostInterface::TITLE => $newTitle],
            'Aheadworks\Blog\Api\Data\PostInterface'
        );

        $requestData = [
            'post' => $this->dataObjectProcessor->buildOutputDataArray(
                $postDataObject,
                'Aheadworks\Blog\Api\Data\PostInterface'
            )
        ];
        $post = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($post['id']);
        $this->assertEquals($postDataObject->getTitle(), $newTitle);
    }

    /**
     * Test delete \Aheadworks\Blog\Api\Data\PostInterface
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDelete()
    {
        $postDataObject = $this->createPostDataObject();
        $postId = $this->postRepository->save($postDataObject)->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $postId,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteById',
            ],
        ];

        $this->_webApiCall($serviceInfo, ['postId' => $postId]);
        $this->postRegistry->remove($postId);
        $this->postRepository->get($postId);
    }

    /**
     * Test search \Aheadworks\Blog\Api\Data\PostInterface
     */
    public function testSearch()
    {
        $postDataObject = $this->createPostDataObject();
        $this->post = $this->postRepository->save($postDataObject);
        $urlKey = $postDataObject->getUrlKey();

        $filter = $this->filterBuilder
            ->setField(PostInterface::URL_KEY)
            ->setValue($urlKey)
            ->create();
        $this->searchCriteriaBuilder->addFilters([$filter]);

        $searchData = $this->searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/search" . '?' . http_build_query($requestData),
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];

        $searchResult = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $searchResult['total_count']);
        $this->assertEquals($searchResult['items'][0][PostInterface::URL_KEY], $urlKey);
    }

    /**
     * Creates new post data object and fills it with predefined data
     *
     * @return PostInterface
     */
    private function createPostDataObject()
    {
        $title = 'Post title';
        $urlKey = 'post-title' . uniqid();
        $content = 'Post content';
        $status = PostStatus::DRAFT;
        $isAllowComments = true;
        $storeIds = [1];

        /** @var PostInterface $postDataObject */
        $postDataObject = $this->postFactory->create();
        $postDataObject
            ->setTitle($title)
            ->setUrlKey($urlKey)
            ->setContent($content)
            ->setStatus($status)
            ->setIsAllowComments($isAllowComments)
            ->setStoreIds($storeIds);

        return $postDataObject;
    }
}
