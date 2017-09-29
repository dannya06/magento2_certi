<?php
namespace Aheadworks\Blog\Test\ApiFunctional\Api;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Tests for blog category service.
 */
class CategoryRepositoryTest extends WebapiAbstract
{
    const SERVICE_NAME = 'aheadworksBlogCategoryRepositoryV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/blogCategory';

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var \Aheadworks\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Aheadworks\Blog\Model\CategoryRegistry
     */
    private $categoryRegistry;

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
     * @var CategoryInterface|null
     */
    private $category;

    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->categoryFactory = $objectManager->create('Aheadworks\Blog\Api\Data\CategoryInterfaceFactory');
        $this->categoryRepository = $objectManager->create('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $this->categoryRegistry = $objectManager->get('Aheadworks\Blog\Model\CategoryRegistry');
        $this->dataObjectHelper = $objectManager->create('Magento\Framework\Api\DataObjectHelper');
        $this->dataObjectProcessor = $objectManager->create('Magento\Framework\Reflection\DataObjectProcessor');
        $this->filterBuilder = $objectManager->create('Magento\Framework\Api\FilterBuilder');
        $this->searchCriteriaBuilder = $objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
    }

    public function tearDown()
    {
        if ($this->category) {
            $this->categoryRepository->delete($this->category);
            $this->category = null;
        }
    }

    /**
     * Test get \Aheadworks\Blog\Api\Data\CategoryInterface
     */
    public function testGet()
    {
        $categoryDataObject = $this->createCategoryDataObject();
        $this->category = $this->categoryRepository->save($categoryDataObject);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $this->category->getId(),
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];

        $category = $this->_webApiCall($serviceInfo, ['categoryId' => $this->category->getId()]);
        $this->assertNotNull($category['id']);
        $this->assertEquals($this->category->getUrlKey(), $category[CategoryInterface::URL_KEY]);
    }

    /**
     * Test create \Aheadworks\Blog\Api\Data\CategoryInterface
     */
    public function testCreate()
    {
        $categoryDataObject = $this->createCategoryDataObject();
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

        $requestData = ['category' => [
            CategoryInterface::NAME => $categoryDataObject->getName(),
            CategoryInterface::URL_KEY => $categoryDataObject->getUrlKey(),
            CategoryInterface::STATUS => $categoryDataObject->getStatus(),
            CategoryInterface::SORT_ORDER => $categoryDataObject->getSortOrder(),
            CategoryInterface::STORE_IDS => $categoryDataObject->getStoreIds(),
        ]];
        $category = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($category['id']);

        $this->category = $this->categoryRepository->get($category['id']);
        $this->assertEquals($this->category->getUrlKey(), $categoryDataObject->getUrlKey());
    }

    /**
     * Test update \Aheadworks\Blog\Api\Data\CategoryInterface using POST
     */
    public function testUpdateUsingPOST()
    {
        $this->category = $this->categoryRepository->save($this->createCategoryDataObject());
        $this->updateTest(
            $this->category,
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
     * Test update \Aheadworks\Blog\Api\Data\CategoryInterface using PUT
     */
    public function testUpdateUsingPUT()
    {
        $this->category = $this->categoryRepository->save($this->createCategoryDataObject());
        $this->updateTest(
            $this->category,
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $this->category->getId(),
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
     * Test update \Aheadworks\Blog\Api\Data\CategoryInterface
     *
     * @param CategoryInterface $categoryDataObject
     * @param array $serviceInfo
     */
    private function updateTest($categoryDataObject, $serviceInfo)
    {
        $newName = $categoryDataObject->getName() . '-new';

        $this->dataObjectHelper->populateWithArray(
            $categoryDataObject,
            [CategoryInterface::NAME => $newName],
            'Aheadworks\Blog\Api\Data\CategoryInterface'
        );

        $requestData = [
            'category' => $this->dataObjectProcessor->buildOutputDataArray(
                $categoryDataObject,
                'Aheadworks\Blog\Api\Data\CategoryInterface'
            )
        ];
        $category = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($category['id']);
        $this->assertEquals($categoryDataObject->getName(), $newName);
    }

    /**
     * Test delete \Aheadworks\Blog\Api\Data\CategoryInterface
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDelete()
    {
        $categoryDataObject = $this->createCategoryDataObject();
        $categoryId = $this->categoryRepository->save($categoryDataObject)->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $categoryId,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteById',
            ],
        ];

        $this->_webApiCall($serviceInfo, ['categoryId' => $categoryId]);
        $this->categoryRegistry->remove($categoryId);
        $this->categoryRepository->get($categoryId);
    }

    /**
     * Test search \Aheadworks\Blog\Api\Data\CategoryInterface
     */
    public function testSearch()
    {
        $categoryDataObject = $this->createCategoryDataObject();
        $this->category = $this->categoryRepository->save($categoryDataObject);
        $urlKey = $categoryDataObject->getUrlKey();

        $filter = $this->filterBuilder
            ->setField(CategoryInterface::URL_KEY)
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
        $this->assertEquals($searchResult['items'][0][CategoryInterface::URL_KEY], $urlKey);
    }

    /**
     * Creates new category data object and fills it with predefined data
     *
     * @return CategoryInterface
     */
    private function createCategoryDataObject()
    {
        $name = 'Category name';
        $urlKey = 'category-name' . uniqid();
        $status = 1;
        $sortOrder = 0;
        $storeIds = [1];

        /** @var CategoryInterface $categoryDataObject */
        $categoryDataObject = $this->categoryFactory->create();
        $categoryDataObject
            ->setName($name)
            ->setUrlKey($urlKey)
            ->setStatus($status)
            ->setSortOrder($sortOrder)
            ->setStoreIds($storeIds);

        return $categoryDataObject;
    }
}
