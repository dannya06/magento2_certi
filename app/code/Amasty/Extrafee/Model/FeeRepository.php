<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Api\Data\FeeInterfaceFactory;
use Amasty\Extrafee\Api\Data\FeeSearchResultsInterface;
use Amasty\Extrafee\Api\Data\FeeSearchResultsInterfaceFactory;
use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Model\ResourceModel\Fee as ResourceFee;
use Amasty\Extrafee\Model\ResourceModel\Fee\CollectionFactory as FeeCollectionFactory;
use Amasty\Extrafee\Model\ResourceModel\Fee\Grid\Collection;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory as FeeQuoteCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Quote\Model\Quote\Address\TotalFactory;

class FeeRepository implements FeeRepositoryInterface
{
    /** @var ResourceFee  */
    private $resource;

    /** @var  FeeFactory */
    private $feeFactory;

    /** @var FeeSearchResultsInterfaceFactory  */
    private $searchResultsFactory;

    /** @var FeeCollectionFactory  */
    private $feeCollectionFactory;

    /** @var FeeInterfaceFactory  */
    private $feeInterfaceFactory;

    /** @var DataObjectHelper  */
    private $dataObjectHelper;

    /** @var DataObjectProcessor  */
    private $dataObjectProcessor;

    /** @var \Amasty\Extrafee\Model\Rule\RuleRepository  */
    private $ruleRepository;

    /** @var feeQuoteCollectionFactory  */
    private $feeQuoteCollectionFactory;

    /** @var TotalFactory  */
    private $totalFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $fees;

    public function __construct(
        ResourceFee $resource,
        FeeFactory $feeFactory,
        FeeSearchResultsInterfaceFactory $searchResultsFactory,
        FeeCollectionFactory $feeCollectionFactory,
        FeeInterfaceFactory $feeInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        \Amasty\Extrafee\Model\Rule\RuleRepository $ruleRepository,
        FeeQuoteCollectionFactory $feeQuoteCollectionFactory,
        TotalFactory $totalFactory
    ) {
        $this->resource = $resource;
        $this->feeFactory = $feeFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->feeCollectionFactory = $feeCollectionFactory;
        $this->feeInterfaceFactory = $feeInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->ruleRepository = $ruleRepository;
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        $this->totalFactory = $totalFactory;
    }

    /**
     * @param FeeInterface $fee
     * @param string[] $options
     * @return FeeInterface
     * @throws CouldNotSaveException
     */
    public function save(FeeInterface $fee, $options)
    {
        try {
            $this->resource
                ->save($fee)
                ->saveOptions($fee, $options)
                ->saveStores($fee)
                ->saveCustomerGroups($fee);

        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the fee: %1', $exception->getMessage()));
        }

        return $fee;
    }

    /**
     * @return FeeInterface
     */
    public function create()
    {
        return $this->feeFactory->create();
    }

    /**
     * @param int $id
     * @return FeeInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->fees[$id])) {
            /** @var Fee $fee */
            $fee = $this->create();
            $this->resource->load($fee, $id);
            if (!$fee->getId()) {
                throw new NoSuchEntityException(__('Fee with id "%1" does not exist.', $id));
            }
            $this->fees[$id] = $fee;
        }

        return $this->fees[$id];
    }

    /**
     * @param FeeInterface $fee
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(FeeInterface $fee)
    {
        try {
            $this->resource->delete($fee);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the fee: %1', $exception->getMessage()));
        }
        return true;
    }

    /**
     * @param int $feeId
     * @return bool
     */
    public function deleteById($feeId)
    {
        return $this->delete($this->getById($feeId));
    }

    /**
     * @param SearchCriteriaInterface|null $criteria
     * @return FeeSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria = null)
    {
        /** @var Collection $collection */
        $collection = $this->feeCollectionFactory->create();
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setTotalCount($collection->getSize());
        $collection->addOrder('sort_order', AbstractDb::SORT_ORDER_ASC);

        if ($criteria) {
            $searchResults->setSearchCriteria($criteria);
            foreach ($criteria->getFilterGroups() as $group) {
                $this->addFilterGroupToCollection($group, $collection);
            }
        }

        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Fee $fee
     * @return bool
     */
    public function validateAddress(\Magento\Quote\Model\Quote $quote, Fee $fee)
    {
        $valid = false;
        $salesRule = $this->getSalesRule($fee);
        $address = $quote->getShippingAddress();

        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        }

        $address->setCollectShippingRates(true);
        $address->collectShippingRates();
        $address->setData('total_qty', $quote->getData('items_qty'));
        $address->setData('base_subtotal', $quote->getData('base_subtotal'));

        if ($salesRule->validate($address)) {
            $valid = true;
        }

        return $valid;
    }

    /**
     * @param FilterGroup $filterGroup
     * @param AbstractCollection $collection
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, AbstractCollection $collection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * @param FeeInterface $fee
     * @return \Amasty\Extrafee\Model\Rule\FeeConditionProcessor
     * @deprecated 1.6.0 use \Amasty\Extrafee\Model\Rule\RuleRepository::getByFee
     * @codeCoverageIgnore
     */
    public function getSalesRule(FeeInterface $fee)
    {
        return $this->ruleRepository->getByFee($fee);
    }

    /**
     * @param int $optionId
     * @return FeeInterface
     * @throws NoSuchEntityException
     */
    public function getByOptionId($optionId)
    {
        $connection = $this->resource->getConnection();

        $tableName = $this->resource->getTable(\Amasty\Extrafee\Model\ResourceModel\Option::TABLE_NAME);
        $select = $connection->select()
            ->from($tableName, 'fee_id')
            ->where(
                'entity_id = ?',
                (int)$optionId
            );

        $data = $connection->fetchRow($select);

        return $this->getById($data['fee_id']);
    }

    /**
     * @param AbstractModel $fee
     * @return AbstractModel
     */
    public function loadOptions(AbstractModel $fee)
    {
        return $this->resource->loadOptions($fee);
    }
}
