<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Model\Fee;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddToExistFeesEligibleForRefund
{
    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    public function __construct(FeeRepositoryInterface $feeRepository)
    {
        $this->feeRepository = $feeRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $feeList = $this->feeRepository->getList();
        /** @var Fee $fee */
        foreach ($feeList->getItems() as $fee) {
            $fee->setIsEligibleForRefund(true);
            $this->feeRepository->save($fee, []);
        }
    }
}
