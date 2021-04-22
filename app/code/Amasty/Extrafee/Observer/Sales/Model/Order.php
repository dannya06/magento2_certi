<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Observer\Sales\Model;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Api\ExtrafeeQuoteRepositoryInterface;
use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Model\ExtrafeeQuoteRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class Order implements ObserverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    /**
     * @var ExtrafeeQuoteRepository
     */
    private $extrafeeQuoteRepository;

    public function __construct(
        SearchCriteriaBuilder $criteriaBuilder,
        CartRepositoryInterface $quoteRepository,
        FeeRepositoryInterface $feeRepository,
        ExtrafeeQuoteRepositoryInterface $extrafeeQuoteRepository
    ) {
        $this->criteriaBuilder = $criteriaBuilder;
        $this->quoteRepository = $quoteRepository;
        $this->feeRepository = $feeRepository;
        $this->extrafeeQuoteRepository = $extrafeeQuoteRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $quote = $this->quoteRepository->get($observer->getOrder()->getQuoteId());
        $criteria = $this->criteriaBuilder->addFilter(FeeInterface::ENABLED, true)
            ->addFilter(FeeInterface::IS_REQUIRED, true)
            ->addFilter(FeeInterface::STORE_ID, [0, $quote->getStoreId()], 'in')
            ->addFilter(FeeInterface::CUSTOMER_GROUP_ID, $quote->getCustomerGroupId())
            ->create();

        $feeItems = $this->feeRepository->getList($criteria);
        $requiredFeeIds = [];
        foreach ($feeItems->getItems() as $fee) {
            if ($this->feeRepository->validateAddress($quote, $fee)) {
                $requiredFeeIds[] = $fee->getId();
            }
        }
        $this->extrafeeQuoteRepository->checkChosenOptions($quote->getId(), $requiredFeeIds);
    }
}
