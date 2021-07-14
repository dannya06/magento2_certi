<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

/**
 * For create empty quote fee and apply the selected fee
 */
class TotalsInformationManagement implements \Amasty\Extrafee\Api\TotalsInformationManagementInterface
{
    /** @var \Magento\Quote\Api\CartTotalRepositoryInterface */
    protected $cartTotalRepository;

    /** @var \Magento\Quote\Api\CartRepositoryInterface */
    protected $cartRepository;

    /** @var \Amasty\Extrafee\Model\ExtrafeeQuoteFactory */
    protected $feeQuoteFactory;

    /** @var \Amasty\Extrafee\Api\FeeRepositoryInterface */
    protected $feeRepository;

    /** @var \Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory */
    protected $feeQuoteCollectionFactory;

    /** @var \Magento\Framework\Convert\DataObject */
    protected $objectConverter;

    /** @var \Amasty\Extrafee\Model\ResourceModel\Fee\CollectionFactory */
    protected $feeCollectionFactory;

    /** @var \Magento\Checkout\Model\TotalsInformationManagement */
    protected $checkoutTotalsInformationManagement;

    /**
     * @var ExtrafeeQuoteRepository
     */
    private $feeQuoteRepository;

    /**
     * @var OptionManager
     */
    private $optionManager;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository,
        \Amasty\Extrafee\Api\FeeRepositoryInterface $feeRepository,
        \Amasty\Extrafee\Model\ExtrafeeQuoteFactory $feeQuoteFactory,
        \Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory $feeQuoteCollectionFactory,
        \Amasty\Extrafee\Model\ResourceModel\Fee\CollectionFactory $feeCollectionFactory,
        \Magento\Framework\Convert\DataObject $objectConverter,
        \Magento\Checkout\Model\TotalsInformationManagement $checkoutTotalsInformationManagement,
        ExtrafeeQuoteRepository $feeQuoteRepository,
        OptionManager $optionManager
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->feeRepository = $feeRepository;
        $this->feeQuoteFactory = $feeQuoteFactory;
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        $this->objectConverter = $objectConverter;
        $this->checkoutTotalsInformationManagement = $checkoutTotalsInformationManagement;
        $this->feeCollectionFactory = $feeCollectionFactory;
        $this->feeQuoteRepository = $feeQuoteRepository;
        $this->optionManager = $optionManager;
    }

    /**
     * Create empty quote fee with calculate checkout Totals
     *
     * @param int $cartId
     * @param \Amasty\Extrafee\Api\Data\TotalsInformationInterface $information
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function calculate(
        $cartId,
        \Amasty\Extrafee\Api\Data\TotalsInformationInterface $information,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $quote->setAppliedAmastyFeeFlag(true);

        $optionsIds = $information->getOptionsIds();
        $feeId = $information->getFeeId();

        $this->proceedQuoteOptions($quote, $feeId, $optionsIds);

        return $this->checkoutTotalsInformationManagement->calculate($cartId, $addressInformation);
    }

    /**
     * Create empty quote fee
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param string|int $feeId
     * @param array $optionsIds
     */
    public function proceedQuoteOptions(\Magento\Quote\Model\Quote $quote, $feeId, $optionsIds)
    {
        if (is_array($optionsIds)) {
            $quoteId = $quote->getId();
            $fee = $this->feeRepository->getById($feeId);

            //only checkbox type allow multifee mode
            if ($fee->getFrontendType() !== Fee::FRONTEND_TYPE_CHECKBOX) {
                $optionsIds = array_slice($optionsIds, 0, 1);
            }

            $optionsIds = $this->checkExistOptions($fee, $optionsIds);

            $collection = $this->feeQuoteCollectionFactory->create()
                ->addFilterByFeeAndQuote($fee->getId(), $quoteId);
            $existingOptionIds = [];
            foreach ($collection->getItems() as $feeQuote) {
                $existingOptionId = $feeQuote->getOptionId();
                if ($existingOptionId == 0) {
                    continue;
                }

                if (!in_array($existingOptionId, $optionsIds)) {
                    $this->feeQuoteRepository->delete($feeQuote);
                    continue;
                }

                $existingOptionIds[] = $existingOptionId;
            }

            $notExistOptions = array_diff($optionsIds, $existingOptionIds);
            foreach ($notExistOptions as $optionId) {
                $feeQuote = $this->feeQuoteFactory->create();
                $feeQuote->addData([
                    'quote_id' => $quoteId,
                    'fee_id' => $fee->getId(),
                    'option_id' => $optionId,
                ]);
                $this->feeQuoteRepository->save($feeQuote);
            }
        }
    }

    /**
     * Apply the selected fee and tax
     *
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function updateQuoteFees(\Magento\Quote\Model\Quote $quote)
    {
        /** @var \Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\Collection $feesQuoteCollection */
        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('option_id', ['neq' => '0'])
            ->addFieldToFilter('quote_id', $quote->getId());

        // @TODO overload maybe
        $feesIds = $this->objectConverter->toOptionHash(
            $this->getQuoteFeesItems($quote->getId()),
            'option_id',
            'fee_id'
        );

        /** @var Fee[] $feesItems */
        $feesItems = $this->feeCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => array_unique($feesIds)])
            ->getItems();

        /** @var ExtrafeeQuote $feesQuoteItem */
        foreach ($feesQuoteCollection->getItems() as $feesQuoteItem) {
            if (array_key_exists($feesQuoteItem->getFeeId(), $feesItems)) {
                $fee = $feesItems[$feesQuoteItem->getFeeId()];

                // @TODO overload options for fee if there are more than 1 options selected
                $this->feeRepository->loadOptions($fee);
                // @TODO also overload
                $baseOptions = $this->optionManager->fetchBaseOptions($quote, $fee);
                $option = $this->findOption($baseOptions, $feesQuoteItem->getOptionId());
                /** if data changed update storage */
                if ($option['price'] !== $feesQuoteItem->getFeeAmount() ||
                    $option['base_price'] !== $feesQuoteItem->getBaseFeeAmount() ||
                    $option['tax'] !== $feesQuoteItem->getTaxAmount() ||
                    $option['base_tax'] !== $feesQuoteItem->getBaseTaxAmount() ||
                    $option['label'] !== $feesQuoteItem->getLabel()
                ) {
                    $feesQuoteItem->setFeeAmount($option['price'])
                        ->setBaseFeeAmount($option['base_price'])
                        ->setTaxAmount($option['tax'])
                        ->setBaseTaxAmount($option['base_tax'])
                        ->setLabel($option['label']);

                    $this->feeQuoteRepository->save($feesQuoteItem);
                }
            }
        }
    }

    /**
     * @param int $quoteId
     * @return \Magento\Framework\DataObject[]
     */
    private function getQuoteFeesItems($quoteId)
    {
        return $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quoteId)->getItems();
    }

    /**
     * @param Fee $fee
     * @param array $optionsIds
     * @return array
     */
    private function checkExistOptions(Fee $fee, $optionsIds)
    {
        foreach ($optionsIds as $key => $optionId) {
            if (!in_array($optionId, $fee->getOptionsIds())) {
                unset($optionsIds[$key]);
            }
        }

        return $optionsIds;
    }

    /**
     * @param array $options
     * @param $optionId
     * @return null
     */
    private function findOption(array $options, $optionId)
    {
        $option = null;

        foreach ($options as $item) {
            if ((int)$item['index'] === (int)$optionId) {
                $option = $item;
                break;
            }
        }

        return $option;
    }
}
