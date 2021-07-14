<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Plugin\Paypal\Model\Express;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Api\ExtrafeeQuoteRepositoryInterface;
use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Model\ExtrafeeQuoteRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Paypal\Model\Express\Checkout;
use Magento\Checkout\Model\Session;

class CheckoutPlugin
{
    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var ExtrafeeQuoteRepositoryInterface
     */
    private $extrafeeQuoteRepository;

    /**
     * @var Session
     */
    private $checkoutSession;

    public function __construct(
        FeeRepositoryInterface $feeRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        ExtrafeeQuoteRepositoryInterface $extrafeeQuoteRepository,
        Session $checkoutSession
    ) {
        $this->feeRepository = $feeRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->extrafeeQuoteRepository = $extrafeeQuoteRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Checkout $subject
     * @param $token
     * @param string|null $payerIdentifier
     */
    public function beforeReturnFromPaypal(Checkout $subject, $token, string $payerIdentifier = null)
    {
        $requiredFeeIds = [];
        $criteria = $this->criteriaBuilder->addFilter(FeeInterface::ENABLED, true)
            ->addFilter(FeeInterface::IS_REQUIRED, true)
            ->create();

        $quote = $this->checkoutSession->getQuote();
        $feeItems = $this->feeRepository->getList($criteria);
        foreach ($feeItems->getItems() as $fee) {
            if ($this->feeRepository->validateAddress($quote, $fee)) {
                if ($fee->isRequired()) {
                    $requiredFeeIds[] = $fee->getId();
                }
            }
        }
        $this->extrafeeQuoteRepository->checkChosenOptions($quote->getId(), $requiredFeeIds);
    }
}
