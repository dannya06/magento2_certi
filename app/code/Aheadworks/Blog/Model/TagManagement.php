<?php
namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Api\TagManagementInterface;
use Aheadworks\Blog\Model\Config;

/**
 * Tag Management
 */
class TagManagement implements TagManagementInterface
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagInterfaceFactory
     */
    private $tagDataFactory;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * TagManagement constructor.
     *
     * @param ResourceModel\Tag\CollectionFactory $collectionFactory
     * @param \Aheadworks\Blog\Model\Config $config
     * @param \Aheadworks\Blog\Api\Data\TagInterfaceFactory $tagDataFactory
     * @param \Aheadworks\Blog\Api\Data\TagSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        \Aheadworks\Blog\Model\ResourceModel\Tag\CollectionFactory $collectionFactory,
        Config $config,
        \Aheadworks\Blog\Api\Data\TagInterfaceFactory $tagDataFactory,
        \Aheadworks\Blog\Api\Data\TagSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->tagDataFactory = $tagDataFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getCloudTags($storeId, $categoryId = null)
    {
        $size = $this->config->getValue(Config::XML_SIDEBAR_POPULAR_TAGS);

        /** @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->joinCount($storeId)
            ->addOrder('count', 'DESC')
            ->setPageSize($size);
        if ($categoryId) {
            $collection->addCategoryFilter($categoryId);
        }

        $tags = [];
        foreach ($collection as $tagModel) {
            $tags[] = $this->getTagDataObject($tagModel);
        }

        /** @var \Aheadworks\Blog\Api\Data\TagSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults
            ->setItems($tags)
            ->setTotalCount($collection->getSize());
        // todo: fix get size counting
        return $searchResults;
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
