<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Model\Fee;
use Magento\Framework\Api\SearchCriteriaBuilder;

class FeeEligibleDataProvider
{
    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(FeeRepositoryInterface $feeRepository, SearchCriteriaBuilder $searchCriteriaBuilder)
    {
        $this->feeRepository = $feeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return array
     */
    public function getEligibleIdsForRefund(): array
    {
        $eligibleIds = [];

        $this->searchCriteriaBuilder->addFilter(FeeInterface::IS_ELIGIBLE_REFUND, 1);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $feeList = $this->feeRepository->getList($searchCriteria);
        /** @var Fee $fee */
        foreach ($feeList->getItems() as $fee) {
            $eligibleIds[] = $fee->getId();
        }

        return $eligibleIds;
    }
}
