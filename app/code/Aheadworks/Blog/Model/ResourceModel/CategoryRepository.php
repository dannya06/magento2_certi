<?php
namespace Aheadworks\Blog\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\CategoryInterface;

/**
 * Category repository.
 */
class CategoryRepository implements \Aheadworks\Blog\Api\CategoryRepositoryInterface
{
    /**
     * @var \Aheadworks\Blog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategoryInterfaceFactory
     */
    private $categoryDataFactory;

    /**
     * @var \Aheadworks\Blog\Model\CategoryRegistry
     */
    private $categoryRegistry;

    /**
     * @var \Aheadworks\Blog\Api\Data\CategorySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * CategoryRepository constructor.
     *
     * @param \Aheadworks\Blog\Model\CategoryFactory $categoryFactory
     * @param \Aheadworks\Blog\Api\Data\CategoryInterfaceFactory $categoryDataFactory
     * @param \Aheadworks\Blog\Model\CategoryRegistry $categoryRegistry
     * @param \Aheadworks\Blog\Api\Data\CategorySearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        \Aheadworks\Blog\Model\CategoryFactory $categoryFactory,
        \Aheadworks\Blog\Api\Data\CategoryInterfaceFactory $categoryDataFactory,
        \Aheadworks\Blog\Model\CategoryRegistry $categoryRegistry,
        \Aheadworks\Blog\Api\Data\CategorySearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryDataFactory = $categoryDataFactory;
        $this->categoryRegistry = $categoryRegistry;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Aheadworks\Blog\Api\Data\CategoryInterface $category)
    {
        /** @var \Aheadworks\Blog\Model\Category $categoryModel */
        $categoryModel = $this->categoryFactory->create();
        if ($category->getId()) {
            $categoryModel->load($category->getId());
        }
        $categoryModel
            ->addData(
                $this->dataObjectProcessor->buildOutputDataArray(
                    $category,
                    'Aheadworks\Blog\Api\Data\CategoryInterface'
                )
            )
            ->save();
        $this->categoryRegistry->push($categoryModel);
        return $this->get($categoryModel->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get($categoryId)
    {
        $categoryModel = $this->categoryRegistry->retrieve($categoryId);
        return $this->getCategoryDataObject($categoryModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getByUrlKey($urlKey)
    {
        $categoryModel = $this->categoryRegistry->retrieveByUrlKey($urlKey);
        return $this->getCategoryDataObject($categoryModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Aheadworks\Blog\Api\Data\CategorySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->categoryFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, 'Aheadworks\Blog\Api\Data\CategoryInterface');
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === CategoryInterface::STORE_IDS) {
                    $collection->addStoreFilter($filter->getValue());
                } else {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $fields[] = $filter->getField();
                    $conditions[] = [$condition => $filter->getValue()];
                }
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $collection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $categories = [];
        /** @var \Aheadworks\Blog\Model\Category $categoryModel */
        foreach ($collection as $categoryModel) {
            $categories[] = $this->getCategoryDataObject($categoryModel);
        }
        $searchResults->setItems($categories);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Aheadworks\Blog\Api\Data\CategoryInterface $category)
    {
        return $this->deleteById($category->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($categoryId)
    {
        $category = $this->categoryRegistry->retrieve($categoryId);
        $category->delete();
        $this->categoryRegistry->remove($categoryId);
        return true;
    }

    /**
     * Retrieves category data object using Category Model
     *
     * @param \Aheadworks\Blog\Model\Category $category
     * @return \Aheadworks\Blog\Api\Data\CategoryInterface
     */
    private function getCategoryDataObject(\Aheadworks\Blog\Model\Category $category)
    {
        /** @var \Aheadworks\Blog\Api\Data\CategoryInterface $categoryDataObject */
        $categoryDataObject = $this->categoryDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $categoryDataObject,
            $category->getData(),
            'Aheadworks\Blog\Api\Data\CategoryInterface'
        );
        $categoryDataObject->setId($category->getId());

        return $categoryDataObject;
    }
}
