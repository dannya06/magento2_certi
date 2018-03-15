<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterface;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterfaceFactory;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\Filter as FilterModel;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Collection as FilterCollection;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\CollectionFactory as FilterCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class FilterRepository
 * @package Aheadworks\Layerednav\Model\ResourceModel
 */
class FilterRepository implements FilterRepositoryInterface
{
    /**
     * @var FilterInterface[]
     */
    private $instances = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var FilterInterfaceFactory
     */
    private $filterFactory;

    /**
     * @var FilterCollectionFactory
     */
    private $filterCollectionFactory;

    /**
     * @var FilterSearchResultsInterfaceFactory
     */
    private $filterSearchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param EntityManager $entityManager
     * @param FilterInterfaceFactory $filterFactory
     * @param FilterCollectionFactory $filterCollectionFactory
     * @param FilterSearchResultsInterfaceFactory $filterSearchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        EntityManager $entityManager,
        FilterInterfaceFactory $filterFactory,
        FilterCollectionFactory $filterCollectionFactory,
        FilterSearchResultsInterfaceFactory $filterSearchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->entityManager = $entityManager;
        $this->filterFactory = $filterFactory;
        $this->filterCollectionFactory = $filterCollectionFactory;
        $this->filterSearchResultsFactory = $filterSearchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(FilterInterface $filter, $storeId = null)
    {
        try {
            $this->entityManager->save($filter);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        unset($this->instances[$filter->getId()]);

        $storeId = $storeId ? : $this->storeManager->getStore()->getId();
        return $this->get($filter->getId(), $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function get($filterId, $storeId = null)
    {
        if (!isset($this->instances[$filterId])) {
            /** @var FilterInterface $filter */
            $filter = $this->filterFactory->create();

            $storeId = $storeId ? : $this->storeManager->getStore()->getId();
            $arguments = ['store_id' => $storeId];

            $this->entityManager->load($filter, $filterId, $arguments);
            if (!$filter->getId()) {
                throw NoSuchEntityException::singleField('id', $filterId);
            }
            $this->instances[$filterId] = $filter;
        }
        return $this->instances[$filterId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($code, $type, $storeId = null)
    {
        /** @var FilterCollection $filterCollection */
        $filterCollection = $this->filterCollectionFactory->create();
        $filterCollection
            ->addFilterByCode($code)
            ->addFilterByType($type);
        /** @var FilterModel $filter */
        $filter = $filterCollection->getFirstItem();
        if (!$filter->getId()) {
            throw NoSuchEntityException::singleField('code', $code);
        }

        $storeId = $storeId ? : $this->storeManager->getStore()->getId();
        return $this->get($filter->getId(), $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var FilterSearchResultsInterface $searchResults */
        $searchResults = $this->filterSearchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);

        /** @var FilterCollection $collection */
        $collection = $this->filterCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, FilterInterface::class);

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

        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $storeId = $storeId ? : $this->storeManager->getStore()->getId();
        $collection
            ->setStoreId($storeId)
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $filters = [];
        /** @var FilterModel $filterModel */
        foreach ($collection as $filterModel) {
            /** @var FilterInterface $filter */
            $filter = $this->filterFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $filter,
                $filterModel->getData(),
                FilterInterface::class
            );
            $filters[] = $filter;
        }

        $searchResults
            ->setItems($filters)
            ->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(FilterInterface $filter)
    {
        return $this->deleteById($filter->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($filterId)
    {
        /** @var FilterInterface $filter */
        $filter = $this->get($filterId);
        $this->entityManager->delete($filter);
        unset($this->instances[$filterId]);

        return true;
    }
}
