<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\FeesInformationManagementInterface;
use Amasty\Extrafee\Model\Data\FeesManagerFactory;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory as FeeQuoteCollectionFactory;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\Collection;
use Magento\Checkout\Model\TotalsInformationManagement as CheckoutTotalsInformationManagement;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * For load all fees and choice default or quote fees
 */
class FeesInformationManagement implements FeesInformationManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var FeeRepository
     */
    protected $feeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var CheckoutTotalsInformationManagement
     */
    protected $checkoutTotalsInformationManagement;

    /**
     * @var FeesManagerFactory
     */
    protected $feesManagerFactory;

    /**
     * @var FeeQuoteCollectionFactory
     */
    private $feeQuoteCollectionFactory;

    /**
     * @var ExtrafeeQuoteRepository
     */
    private $feeQuoteRepository;

    /**
     * @var ExtrafeeQuoteFactory
     */
    private $quoteFeeFactory;

    /**
     * @var OptionManager
     */
    private $optionManager;

    /**
     * @var array
     */
    private $loadedFees = [];

    public function __construct(
        CartRepositoryInterface $cartRepository,
        FeeRepository $feeRepository,
        ExtrafeeQuoteRepository $feeQuoteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        FilterBuilder $filterBuilder,
        CheckoutTotalsInformationManagement $checkoutTotalsInformationManagement,
        FeesManagerFactory $feesManagerFactory,
        FeeQuoteCollectionFactory $feeQuoteCollectionFactory,
        ExtrafeeQuoteFactory $quoteFeeFactory,
        OptionManager $optionManager
    ) {
        $this->cartRepository = $cartRepository;
        $this->feeRepository = $feeRepository;
        $this->feeQuoteRepository = $feeQuoteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->checkoutTotalsInformationManagement = $checkoutTotalsInformationManagement;
        $this->feesManagerFactory = $feesManagerFactory;
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        $this->quoteFeeFactory = $quoteFeeFactory;
        $this->optionManager = $optionManager;
    }

    /**
     * Load default fees or quote fees with calculate checkout Totals
     *
     * @param int $cartId
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @return \Amasty\Extrafee\Api\Data\FeesManagerInterface
     */
    public function collect(
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        try {
            $totals = $this->checkoutTotalsInformationManagement->calculate($cartId, $addressInformation);
            $quote = $this->cartRepository->get($cartId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        if (!$this->loadedFees) {
            //getting and validating fees according to current quote
            $this->loadedFees = $this->collectQuote($quote);
        }

        //recalculate quote totals according to just loaded extra fees
        $quote->setTotalsCollectedFlag(false);
        if (!count($this->loadedFees)) {
            $segments = $totals->getTotalSegments();
            unset($segments['amasty_extrafee']);
            $totals->setTotalSegments($segments);
        }

        return $this->feesManagerFactory->create()
            ->setFees($this->loadedFees)
            ->setTotals($totals);
    }

    /**
     * Load default fees or quote fees
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    public function collectQuote(\Magento\Quote\Model\Quote $quote)
    {
        $criteria = $this->prepareSearchCriteria($quote->getStoreId(), $quote->getCustomerGroupId());
        $feeCollection = $this->feeRepository->getList($criteria);
        $quoteId = $quote->getId();

        $matchedFeesIds = [];
        /** @var Fee $fee */
        foreach ($feeCollection->getItems() as $fee) {
            if (!$this->feeRepository->validateAddress($quote, $fee)) {
                continue;
            }
            $this->feeRepository->loadOptions($fee);
            $baseOptions = $this->optionManager->fetchBaseOptions($quote, $fee);
            $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
                ->addFilterByFeeAndQuote($fee->getId(), $quoteId);
            if ($feesQuoteCollection->count() === 0) {
                $this->applyDefaultFee($feesQuoteCollection, $quoteId, $fee->getId(), $baseOptions);
            }
            $fee->setBaseOptions($baseOptions);
            $fee->setCurrentValue($this->getSelectionOptionsIds($feesQuoteCollection, $fee));

            $matchedFeesIds[] = $fee->getId();
            $this->loadedFees[$fee->getId()] = $fee;
        }
        $this->removeUnmatchedFees($quoteId, $matchedFeesIds);

        return $this->loadedFees;
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     * @return SearchCriteria
     */
    private function prepareSearchCriteria($storeId, $customerGroupId)
    {
        $filterEnabled = $this->filterBuilder->setField('enabled')
            ->setValue('1')
            ->setConditionType('eq')
            ->create();

        $filterStore = $this->filterBuilder->setField('store_id')
            ->setValue(['0', $storeId])
            ->setConditionType('in')
            ->create();

        $filterCustomerGroup = $this->filterBuilder->setField('customer_group_id')
            ->setValue($customerGroupId)
            ->setConditionType('eq')
            ->create();

        $filterGroup = $this->filterGroupBuilder
            ->addFilter($filterEnabled)
            ->addFilter($filterStore)
            ->addFilter($filterCustomerGroup)
            ->create();

        return $this->searchCriteriaBuilder->create()
            ->setFilterGroups([$filterGroup]);
    }

    /**
     * @param Collection $feesQuoteCollection
     * @param int $quoteId
     * @param int $feeId
     * @param array $options
     */
    private function applyDefaultFee($feesQuoteCollection, $quoteId, $feeId, array $options)
    {
        // have to initialize by zero value, for know that initialized, when customer unselect all options
        $quoteFee = $this->quoteFeeFactory->create()
            ->addData([
                'quote_id' => $quoteId,
                'fee_id' => $feeId,
                'option_id' => '0',
                'fee_amount' => '0',
                'base_fee_amount' => '0',
                'label' => ''
            ]);
        $this->feeQuoteRepository->save($quoteFee);
        $feesQuoteCollection->addItem($quoteFee);

        //initialized admin selected option
        foreach ($options as $option) {
            if ($option['default']) {
                $quoteFee = $this->quoteFeeFactory->create();
                $quoteFee->addData([
                    'quote_id' => $quoteId,
                    'fee_id' => $feeId,
                    'option_id' => $option['index'],
                    'label' => $option['label'],
                    'fee_amount' => $option['price'],
                    'base_fee_amount' => $option['base_price'],
                ]);
                $this->feeQuoteRepository->save($quoteFee);
                $feesQuoteCollection->addItem($quoteFee);
                break;
            }
        }
    }

    /**
     * @param int $quoteId
     * @param array $matchedFeesIds
     */
    private function removeUnmatchedFees($quoteId, array $matchedFeesIds)
    {
        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quoteId);

        if (count($matchedFeesIds) > 0) {
            $feesQuoteCollection->addFieldToFilter('fee_id', ['nin' => $matchedFeesIds]);
        }

        foreach ($feesQuoteCollection->getItems() as $feeQuote) {
            $this->feeQuoteRepository->delete($feeQuote);
        }
    }

    /**
     * @param Collection $feesQuoteCollection
     * @param Fee $fee
     * @return array|string|false
     */
    private function getSelectionOptionsIds($feesQuoteCollection, Fee $fee)
    {
        $optionsIds = [];

        $existingOptions = $fee->getOptionsIds();
        foreach ($feesQuoteCollection->getItems() as $feeQuote) {
            if ($feeQuote->getOptionId() == 0) {
                continue;
            }
            if (!in_array($feeQuote->getOptionId(), $existingOptions)) {
                $this->feeQuoteRepository->delete($feeQuote);
                continue;
            }
            $optionsIds[] = $feeQuote->getOptionId();
        }

        return $fee->getFrontendType() === Fee::FRONTEND_TYPE_CHECKBOX ? $optionsIds : end($optionsIds);
    }
}
