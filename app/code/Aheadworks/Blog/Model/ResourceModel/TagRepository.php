<?php
namespace Aheadworks\Blog\Model\ResourceModel;

/**
 * Tag repository.
 */
class TagRepository implements \Aheadworks\Blog\Api\TagRepositoryInterface
{
    /**
     * @var \Aheadworks\Blog\Model\TagFactory
     */
    private $tagFactory;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagInterfaceFactory
     */
    private $tagDataFactory;

    /**
     * @var \Aheadworks\Blog\Model\TagRegistry
     */
    private $tagRegistry;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagSearchResultsInterfaceFactory
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
     * TagRepository constructor.
     *
     * @param \Aheadworks\Blog\Model\TagFactory $tagFactory
     * @param \Aheadworks\Blog\Api\Data\TagInterfaceFactory $tagDataFactory
     * @param \Aheadworks\Blog\Model\TagRegistry $tagRegistry
     * @param \Aheadworks\Blog\Api\Data\TagSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        \Aheadworks\Blog\Model\TagFactory $tagFactory,
        \Aheadworks\Blog\Api\Data\TagInterfaceFactory $tagDataFactory,
        \Aheadworks\Blog\Model\TagRegistry $tagRegistry,
        \Aheadworks\Blog\Api\Data\TagSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->tagFactory = $tagFactory;
        $this->tagDataFactory = $tagDataFactory;
        $this->tagRegistry = $tagRegistry;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Aheadworks\Blog\Api\Data\TagInterface $tag)
    {
        /** @var \Aheadworks\Blog\Model\Tag $tagModel */
        $tagModel = $this->tagFactory->create();
        if ($tag->getId()) {
            $tagModel->load($tag->getId());
        }
        $tagModel
            ->addData(
                $this->dataObjectProcessor->buildOutputDataArray(
                    $tag,
                    'Aheadworks\Blog\Api\Data\TagInterface'
                )
            )
            ->save();
        $this->tagRegistry->push($tagModel);
        return $this->get($tagModel->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get($tagId)
    {
        $tagModel = $this->tagRegistry->retrieve($tagId);
        return $this->getTagDataObject($tagModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($name)
    {
        $tagModel = $this->tagRegistry->retrieveByName($name);
        return $this->getTagDataObject($tagModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Aheadworks\Blog\Api\Data\TagSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection $collection */
        $collection = $this->tagFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, 'Aheadworks\Blog\Api\Data\TagInterface');
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
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

        $tags = [];
        /** @var \Aheadworks\Blog\Model\Tag $tagModel */
        foreach ($collection as $tagModel) {
            $tags[] = $this->getTagDataObject($tagModel);
        }
        $searchResults->setItems($tags);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Aheadworks\Blog\Api\Data\TagInterface $tag)
    {
        return $this->deleteById($tag->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($tagId)
    {
        $post = $this->tagRegistry->retrieve($tagId);
        $post->delete();
        $this->tagRegistry->remove($tagId);
        return true;
    }

    /**
     * Retrieves tag data object using Tag Model
     *
     * @param \Aheadworks\Blog\Model\Tag $tag
     * @return \Aheadworks\Blog\Api\Data\TagInterface
     */
    private function getTagDataObject(\Aheadworks\Blog\Model\Tag $tag)
    {
        /** @var \Aheadworks\Blog\Api\Data\TagInterface $tagDataObject */
        $tagDataObject = $this->tagDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $tagDataObject,
            $tag->getData(),
            'Aheadworks\Blog\Api\Data\TagInterface'
        );
        $tagDataObject->setId($tag->getId());

        return $tagDataObject;
    }
}
