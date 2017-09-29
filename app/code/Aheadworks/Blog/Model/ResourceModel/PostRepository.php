<?php
namespace Aheadworks\Blog\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Disqus;

/**
 * Post repository.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PostRepository implements \Aheadworks\Blog\Api\PostRepositoryInterface
{
    /**
     * @var \Aheadworks\Blog\Model\PostFactory
     */
    private $postFactory;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostInterfaceFactory
     */
    private $postDataFactory;

    /**
     * @var \Aheadworks\Blog\Model\PostRegistry
     */
    private $postRegistry;

    /**
     * @var \Aheadworks\Blog\Api\Data\PostSearchResultsInterfaceFactory
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
     * @var Disqus
     */
    private $disqus;

    /**
     * @var \Magento\Framework\Api\SortOrder
     */
    private $additionalFieldSortOrder;

    /**
     * @var \Magento\Framework\Api\Filter[]
     */
    private $additionalFieldFilters;

    /**
     * PostRepository constructor.
     *
     * @param \Aheadworks\Blog\Model\PostFactory $postFactory
     * @param \Aheadworks\Blog\Api\Data\PostInterfaceFactory $postDataFactory
     * @param \Aheadworks\Blog\Model\PostRegistry $postRegistry
     * @param \Aheadworks\Blog\Api\Data\PostSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param Disqus $disqus
     */
    public function __construct(
        \Aheadworks\Blog\Model\PostFactory $postFactory,
        \Aheadworks\Blog\Api\Data\PostInterfaceFactory $postDataFactory,
        \Aheadworks\Blog\Model\PostRegistry $postRegistry,
        \Aheadworks\Blog\Api\Data\PostSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        Disqus $disqus
    ) {
        $this->postFactory = $postFactory;
        $this->postDataFactory = $postDataFactory;
        $this->postRegistry = $postRegistry;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->disqus = $disqus;
    }

    /**
     * {@inheritdoc}
     */
    public function save(PostInterface $post)
    {
        /** @var \Aheadworks\Blog\Model\Post $postModel */
        $postModel = $this->postFactory->create();
        if ($post->getId()) {
            $postModel->load($post->getId());
        }
        $postModel
            ->addData(
                $this->dataObjectProcessor->buildOutputDataArray(
                    $post,
                    'Aheadworks\Blog\Api\Data\PostInterface'
                )
            )
            ->save();
        $this->postRegistry->push($postModel);
        return $this->get($postModel->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get($postId)
    {
        $postModel = $this->postRegistry->retrieve($postId);
        return $this->getPostDataObject($postModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getByUrlKey($urlKey)
    {
        $postModel = $this->postRegistry->retrieveByUrlKey($urlKey);
        return $this->getPostDataObject($postModel);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        array $additionalFields = []
    ) {
        /** @var \Aheadworks\Blog\Api\Data\PostSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Blog\Model\ResourceModel\Post\Collection $collection */
        $collection = $this->postFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, 'Aheadworks\Blog\Api\Data\PostInterface');
        $this->additionalFieldFilters = [];
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === PostInterface::STORE_IDS) {
                    $collection->addStoreFilter($filter->getValue());
                } elseif ($filter->getField() === PostInterface::CATEGORY_IDS) {
                    $collection->addCategoryFilter($filter->getValue());
                } elseif ($filter->getField() === 'tag_id') {
                    $collection->addTagFilter($filter->getValue());
                } elseif ($filter->getField() === PostInterface::VIRTUAL_STATUS) {
                    $collection->addVirtualStatusFilter($filter->getValue());
                } elseif (in_array($filter->getField(), $additionalFields)) {
                    $this->additionalFieldFilters[] = $filter;
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
        $additionalFieldSortOrders = [];
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                if (in_array($sortOrder->getField(), $additionalFields)) {
                    $additionalFieldSortOrders[] = $sortOrder;
                } elseif ($sortOrder->getField() == PostInterface::VIRTUAL_STATUS) {
                    $collection->addOrder('status', $sortOrder->getDirection());
                    $collection->addOrder('publish_date', $sortOrder->getDirection());
                } else {
                    $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
                }
            }
        }
        $collection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $posts = [];
        $collectionItems = $collection->getItems();
        if (!empty($additionalFields)) {
            /** @var \Aheadworks\Blog\Model\Post $item */
            foreach ($collectionItems as $item) {
                if (in_array('published_comments', $additionalFields)) {
                    $item->setPublishedComments(
                        $this->disqus->getPublishedCommentsNum($item->getId())
                    );
                }
                if (in_array('new_comments', $additionalFields)) {
                    $item->setNewComments($this->disqus->getNewCommentsNum($item->getId()));
                }
            }
            foreach ($additionalFieldSortOrders as $sortOrder) {
                $this->additionalFieldSortOrder = $sortOrder;
                usort($collectionItems, [$this, 'sortByAdditionalField']);
            }
            if (!empty($this->additionalFieldFilters)) {
                $collectionItems = array_filter($collectionItems, [$this, 'filterByAdditionalFields']);
            }
        }
        foreach ($collectionItems as $item) {
            $posts[] = $this->getPostDataObject($item);
        }

        $searchResults->setItems($posts);
        return $searchResults;
    }

    /**
     * Sort posts by additional field
     *
     * @param \Aheadworks\Blog\Model\Post $item1
     * @param \Aheadworks\Blog\Model\Post $item2
     * @return int
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function sortByAdditionalField(\Aheadworks\Blog\Model\Post $item1, \Aheadworks\Blog\Model\Post $item2)
    {
        $result = 0;
        $field = $this->additionalFieldSortOrder->getField();
        $direction = $this->additionalFieldSortOrder->getDirection();

        $item1Data = $item1->getData();
        $item2Data = $item2->getData();

        if (!isset($item1Data[$field])) {
            $result = isset($item1Data[$field]) ? -1 : 0;
        } else {
            if (!isset($item2Data[$field])) {
                $result = 1;
            } else {
                if (is_string($item1Data[$field])) {
                    $result = strnatcmp($item1Data[$field], $item2Data[$field]);
                } elseif (is_numeric($item1Data[$field])) {
                    if ($item1Data[$field] == $item2Data[$field]) {
                        $result = 0;
                    } else {
                        $result = $item1Data[$field] < $item2Data[$field] ? -1 : 1;
                    }
                }
            }
        }

        return strtolower($direction) == 'asc' ? $result : -$result;
    }

    /**
     * Filtering of posts by additional fields
     *
     * @param \Aheadworks\Blog\Model\Post $item
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function filterByAdditionalFields(\Aheadworks\Blog\Model\Post $item)
    {
        $itemData = $item->getData();
        /** @var \Magento\Framework\Api\Filter $filter */
        foreach ($this->additionalFieldFilters as $filter) {
            $field = $filter->getField();
            $condition = $filter->getConditionType();
            $value = $filter->getValue();
            if (is_numeric($value)) {
                if ($condition == 'gteq' && $itemData[$field] < $value
                    || $condition == 'lteq' && $itemData[$field] > $value
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PostInterface $post)
    {
        return $this->deleteById($post->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($postId)
    {
        $post = $this->postRegistry->retrieve($postId);
        $post->delete();
        $this->postRegistry->remove($postId);
        return true;
    }

    /**
     * Retrieves post data object using Post Model
     *
     * @param \Aheadworks\Blog\Model\Post $post
     * @return PostInterface
     */
    private function getPostDataObject(\Aheadworks\Blog\Model\Post $post)
    {
        /** @var PostInterface $postDataObject */
        $postDataObject = $this->postDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $postDataObject,
            $post->getData(),
            'Aheadworks\Blog\Api\Data\PostInterface'
        );
        $postDataObject->setId($post->getId());
        $postDataObject->setVirtualStatus($post->getVirtualStatus());

        return $postDataObject;
    }
}
